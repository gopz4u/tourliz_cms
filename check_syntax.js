const fs = require('fs');
const acorn = require('acorn');

const content = fs.readFileSync('resources/views/admin/b2b/edit.blade.php', 'utf8');
const scriptMatches = content.match(/<script>([\s\S]*?)<\/script>/g);

if (scriptMatches) {
    scriptMatches.forEach((scriptTag, idx) => {
        const sc = scriptTag.replace('<script>', '').replace('</script>', '');
        try {
            // We need to handle Blade variables like {{ ... }} and @json(...)
            // Simple replacement with placeholders to make it valid JS
            let cleanSc = sc.replace(/\{\{.*?\}\}/g, '"blade_val"');
            cleanSc = cleanSc.replace(/@json\(.*?\)/g, '["blade_json"]');

            acorn.parse(cleanSc, { ecmaVersion: 2020 });
            console.log(`Script block ${idx} is valid.`);
        } catch (e) {
            console.log(`Error in script block ${idx}: ${e.message}`);
            // Find context
            const pos = e.pos;
            const context = sc.substring(Math.max(0, pos - 50), Math.min(sc.length, pos + 50));
            console.log(`Context: ...${context}...`);

            // Find line number in script block
            const before = sc.substring(0, pos);
            const lineNo = before.split('\n').length;
            console.log(`Line in script block: ${lineNo}`);
        }
    });
}
