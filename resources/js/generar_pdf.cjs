const { chromium } = require('playwright');

(async () => {
    const html = await new Promise((resolve) => {
        let data = '';
        process.stdin.on('data', chunk => data += chunk);
        process.stdin.on('end', () => resolve(data));
    });

    const browser = await chromium.launch();
    const page = await browser.newPage();

    await page.setContent(html, {
        waitUntil: 'domcontentloaded'
    });

    const pdfBuffer = await page.pdf({
        format: 'A4',
        printBackground: true
    });

    await browser.close();

    process.stdout.write(pdfBuffer);
})();
