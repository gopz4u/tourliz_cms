import re

keywords = ['function', 'return', 'const', 'let', 'window', 'itinerary', 'document', 'calculateDynamicTotal', 'renderBuilder']

file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Focus only on the @push('scripts') block
for match in re.finditer(r"@push\('scripts'\)\s*<script>(.*?)<\/script>", content, re.DOTALL):
    script = match.group(1)
    lines = script.split('\n')
    for i, line in enumerate(lines):
        # Look for split keywords: e.g. "functio n", "retur n"
        for kw in keywords:
            # Create a regex that looks for the keyword split by 1 or more spaces/tabs
            # but only if neither part is a complete word on its own usually
            for split_idx in range(1, len(kw)):
                part1 = kw[:split_idx]
                part2 = kw[split_idx:]
                pattern = rf'\b{part1}\s+{part2}\b'
                if re.search(pattern, line):
                    print(f"Found split keyword '{kw}' ('{part1} {part2}') at script line {i+1}: {line.strip()}")

print("Search complete.")
