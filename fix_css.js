const fs = require('fs');
const cssPath = 'assets/css/conc.css';
let css = fs.readFileSync(cssPath, 'utf8');

css = css.replace(/url\("https:\/\/cdn\.prod\.website-files\.com.*?_hero-image\.jpg"\)/g, 'url("../images/panggonan14.jpeg")');
css = css.replace(/url\("https:\/\/cdn\.prod\.website-files\.com.*?\.jpg"\)/g, 'url("../images/panggonan14.jpeg")');
css = css.replace(/url\("https:\/\/cdn\.prod\.website-files\.com.*?\.png"\)/g, 'url("../images/panggonan14.jpeg")');
css = css.replace(/url\('https:\/\/cdn\.prod\.website-files\.com.*?'\)/g, 'url("../images/panggonan14.jpeg")');

fs.writeFileSync(cssPath, css, 'utf8');
console.log('Fixed CSS URLs');
