const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

async function main() {
    const args = process.argv.slice(2);
    let htmlFile = null;
    let outputFile = null;

    for (let i = 0; i < args.length; i++) {
        if (args[i] === '--html-file' && args[i + 1]) {
            htmlFile = args[i + 1];
            i++;
        } else if (args[i] === '--output' && args[i + 1]) {
            outputFile = args[i + 1];
            i++;
        }
    }

    if (!htmlFile || !outputFile) {
        console.error('Usage: node generate-pdf.cjs --html-file <path> --output <path>');
        process.exit(1);
    }

    const absHtmlFile = path.resolve(htmlFile);
    const absOutputFile = path.resolve(outputFile);

    if (!fs.existsSync(absHtmlFile)) {
        console.error(`HTML file not found: ${absHtmlFile}`);
        process.exit(1);
    }

    fs.mkdirSync(path.dirname(absOutputFile), { recursive: true });

    const htmlContent = fs.readFileSync(absHtmlFile, 'utf8');

    // Detect available Chrome or Edge binary on Windows if needed, or fallback to Puppeteer bundled
    const candidates = [
        process.env.CHROME_BIN,
        process.env.PUPPETEER_EXECUTABLE_PATH,
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
    ].filter(Boolean);

    let executablePath = null;
    for (const cand of candidates) {
        if (fs.existsSync(cand)) {
            executablePath = cand;
            break;
        }
    }

    const launchOptions = {
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--font-render-hinting=medium',
        ],
    };

    if (executablePath) {
        launchOptions.executablePath = executablePath;
    }

    let browser;
    try {
        browser = await puppeteer.launch(launchOptions);
    } catch (e) {
        // Fallback without explicit executablePath
        delete launchOptions.executablePath;
        browser = await puppeteer.launch(launchOptions);
    }

    try {
        const page = await browser.newPage();

        // Emulate print media
        await page.emulateMediaType('print');

        // Set HTML content
        await page.setContent(htmlContent, {
            waitUntil: ['load', 'networkidle0'],
        });

        // Ensure all fonts (including Noto Sans Devanagari) are loaded before snapshotting
        await page.evaluateHandle('document.fonts.ready');

        // Generate PDF
        await page.pdf({
            path: absOutputFile,
            format: 'A4',
            printBackground: true,
            preferCSSPageSize: true,
            margin: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
            },
        });

        console.log(`PDF successfully generated: ${absOutputFile}`);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

main().catch((err) => {
    console.error('Puppeteer generation error:', err);
    process.exit(1);
});
