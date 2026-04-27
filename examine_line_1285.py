with open(r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

line = lines[1284] # 1285
print(f"Line 1285: {repr(line)}")
for i, c in enumerate(line):
    print(f"{i}: {repr(c)}")
