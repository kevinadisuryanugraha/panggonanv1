const fs = require('fs');

let html = fs.readFileSync('index.html', 'utf8');

// The multi-line matches using regex [\s\S]*? to capture accidental spaces/newlines.

html = html.replace(/Our extensive experience enables us to deliver top-tier[\s\S]*?within budget\./g, 'Kami mengangkat sejarah benda untuk membuat mereka kembali legenda.');

html = html.replace(/Our team is always at the forefront of construction technology[\s\S]*?embracing/g, 'Anda diajak untuk berpartisipasi menuliskan inspirasi quote kehidupan di area kami.');

html = html.replace(/We believe in building a better future\. Our projects incorporate sustainable materials[\s\S]*?reducing \./g, 'Membangun estetika modern melalui material bekas yang didaur ulang dengan hati, untuk menciptakan ruang berekspresi.');

// Testimonials
html = html.replace(/Conc exceeded our expectations[\s\S]*?from design to completion/g, 'Panggonan memberi saya ruang untuk melihat barang-barang bekas dengan cara yang sama sekali berbeda.');
html = html.replace(/a home built to perfection/g, 'tempat yang benar-benar membumi dan inspiratif');
html = html.replace(/Commercial Property Investor/g, 'Creative Worker');

// Let's also enforce no "At Conc" is around at all:
html = html.replace(/At Conc/g, 'Di Panggonan');

fs.writeFileSync('index.html', html, 'utf8');
console.log('Done deep regex fixes');
