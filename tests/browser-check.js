async (page) => {
  const assert = (condition, message) => { if (!condition) throw new Error(message); };
  const results = [];
  const metrics = () => page.locator('.psf').first().evaluate(root => {
    const track = root.querySelector('.psf__track');
    const cards = [...root.querySelectorAll('.psf-card:not([hidden])')];
    const rect = track.getBoundingClientRect();
    const card = cards[0].getBoundingClientRect();
    const photo = cards[0].querySelector('.psf-card__image').getBoundingClientRect();
    return {
      viewport: innerWidth, track: rect.width, card: card.width,
      visible: cards.filter(el => { const r = el.getBoundingClientRect(); return r.left >= rect.left - 1 && r.right <= rect.right + 1; }).length,
      nextHint: cards[1] ? rect.right - cards[1].getBoundingClientRect().left : 0,
      photoRatio: photo.width / photo.height,
      arrows: getComputedStyle(root.querySelector('.psf__arrow')).display,
      actions: [...cards[0].querySelectorAll('.psf-card__actions > *')].map(el => el.getBoundingClientRect().height),
      metaRows: new Set([...cards[0].querySelectorAll('.psf-card__meta > span')].map(el => el.getBoundingClientRect().top)).size,
      overflow: document.documentElement.scrollWidth > innerWidth,
      snap: getComputedStyle(track).scrollSnapType,
      brokenImages: [...root.querySelectorAll('img')].filter(el => el.complete && !el.naturalWidth).length
    };
  });
  await page.goto('http://127.0.0.1:9400/');
  await page.locator('.psf[data-psf-ready="1"]').waitFor();
  await page.evaluate(() => localStorage.clear());
  await page.reload();
  for (const width of [1440, 1024, 768, 390, 320]) {
    await page.setViewportSize({ width, height: 900 });
    await page.locator('.psf__track').evaluate(el => el.scrollTo({left: 0, behavior: 'instant'}));
    await page.waitForTimeout(150);
    const heightGap = await page.locator('.elementor-widget-psf_property_slider').evaluate(el => el.getBoundingClientRect().height - el.querySelector('.psf').getBoundingClientRect().height);
    assert(Math.abs(heightGap) < 2, 'Elementor reserves excess widget height');
    const m = await metrics();
    assert(!m.overflow && !m.brokenImages && m.metaRows === 1, 'Layout or image failure at ' + width);
    assert(m.actions.every(h => h >= 44), 'Touch targets too small');
    assert(m.snap === 'x mandatory', 'Missing snap');
    if (width === 1440) assert(m.visible === 4, 'Expected 4 desktop cards');
    else if (width >= 768) assert(m.visible === 2 && m.nextHint > 0, 'Expected tablet cards');
    else {
      assert(m.visible === 1 && m.nextHint > 0 && m.arrows === 'none', 'Mobile hint/arrows');
      assert(m.card / width >= .88 && m.card / width <= .90, 'Mobile width outside 88–90vw');
      assert(Math.abs(m.photoRatio - 4/3) < .01, 'Mobile photo is not 4:3');
    }
    results.push(m);
    if ([1440, 768, 390].includes(width)) await page.screenshot({path: `output/playwright/elementor-${width}.png`, fullPage: true});
  }
  await page.setViewportSize({width:1440,height:900});
  await page.getByRole('button', {name:'Next properties', exact:true}).click();
  await page.waitForTimeout(600);
  assert(await page.locator('.psf__track').evaluate(el => el.scrollLeft > 100), 'Arrow does not scroll');
  assert(await page.getByRole('button',{name:'Next properties',exact:true}).isDisabled(), 'End arrow not disabled');
  await page.getByRole('button',{name:'To rent',exact:true}).click();
  assert(await page.locator('.psf-card:visible').count() === 1, 'Rent filter');
  assert(await page.getByRole('button',{name:'To rent',exact:true}).evaluate(el => getComputedStyle(el).backgroundColor === 'rgba(0, 0, 0, 0)'), 'Theme focus color leaks into filter');
  assert(await page.locator('.psf__track').evaluate(el => el.scrollLeft === 0), 'Filter did not reset position');
  await page.getByRole('button',{name:'For sale',exact:true}).click();
  const save = page.getByRole('button',{name:'Save',exact:true}).first();
  await save.click();
  assert(await save.getAttribute('aria-pressed') === 'true', 'Save toggle');
  await page.reload();
  assert(await save.getAttribute('aria-pressed') === 'true', 'Save persistence');
  await save.click();
  // Test newly rendered widget through Elementor's actual frontend hook.
  await page.locator('.elementor-widget-psf_property_slider').evaluate(widget => {
    const copy = widget.cloneNode(true);
    copy.querySelector('.psf').removeAttribute('data-psf-ready');
    widget.after(copy);
    window.elementorFrontend.hooks.doAction('frontend/element_ready/psf_property_slider.default', window.jQuery(copy));
  });
  assert(await page.locator('.psf[data-psf-ready="1"]').count() === 2, 'Elementor rerender hook');
  await page.locator('.psf').nth(1).getByRole('button',{name:'To rent',exact:true}).click();
  assert(await page.locator('.psf').first().locator('.psf-card:visible').count() === 5, 'Instances affect each other');
  await page.locator('.elementor-widget-psf_property_slider').nth(1).evaluate(el => el.remove());
  // Deliberately conflicting late theme CSS, including structural !important rules.
  await page.addStyleTag({content:`.elementor a {color:purple; text-decoration:underline;} .elementor button {padding:30px; border-radius:99px; font-size:28px; text-transform:uppercase;} .elementor h2 {margin:60px; font-size:70px;} .elementor img {height:auto!important; width:50%!important;} .elementor .psf * {box-sizing:content-box!important;}`});
  await page.setViewportSize({width:390,height:900});
  let m = await metrics();
  assert(m.actions.every(h=>h===48) && Math.abs(m.photoRatio-4/3)<.01 && !m.overflow, 'CSS collision regression');
  assert(await page.locator('.psf-card__image img').first().evaluate(el => Math.abs(el.getBoundingClientRect().height-el.parentElement.getBoundingClientRect().height)<1), 'Theme overrides image crop');
  results.push({check:'late theme CSS',passed:true});
  await page.reload();
  // Native browser touch input, not a JS scrollTo masquerading as a swipe.
  const cdp = await page.context().newCDPSession(page);
  await cdp.send('Emulation.setTouchEmulationEnabled',{enabled:true,maxTouchPoints:1});
  const photo = await page.locator('.psf-card__image').first().boundingBox();
  const y = photo.y + 100;
  await cdp.send('Input.dispatchTouchEvent',{type:'touchStart',touchPoints:[{x:320,y}]});
  for (const x of [290,250,210,170,130,90]) {
    await cdp.send('Input.dispatchTouchEvent',{type:'touchMove',touchPoints:[{x,y}]});
    await page.waitForTimeout(30);
  }
  await cdp.send('Input.dispatchTouchEvent',{type:'touchEnd',touchPoints:[]});
  await page.waitForTimeout(900);
  const swipe = await page.locator('.psf__track').evaluate(el => {
    const first = el.querySelector('.psf-card:not([hidden])');
    const step = first.getBoundingClientRect().width + parseFloat(getComputedStyle(el).columnGap);
    return {left:el.scrollLeft,step,snapError:Math.abs(el.scrollLeft / step - Math.round(el.scrollLeft / step))};
  });
  assert(swipe.left > 100 && swipe.snapError < .01, 'Touch swipe did not snap');
  results.push({check:'Chromium synthesized touch swipe',...swipe});
  // Page-axis gesture must still scroll the containing page.
  await page.addStyleTag({content:'body {min-height:1800px;}'});
  await cdp.send('Input.dispatchTouchEvent',{type:'touchStart',touchPoints:[{x:180,y:360}]});
  for (const y2 of [330,290,250,210,170]) {
    await cdp.send('Input.dispatchTouchEvent',{type:'touchMove',touchPoints:[{x:180,y:y2}]});
    await page.waitForTimeout(30);
  }
  await cdp.send('Input.dispatchTouchEvent',{type:'touchEnd',touchPoints:[]});
  await page.waitForTimeout(400);
  assert(await page.evaluate(()=>scrollY>0), 'Vertical touch scroll blocked');
  results.push({check:'vertical touch page scroll',passed:true});
  await cdp.detach();
  return results;
}
