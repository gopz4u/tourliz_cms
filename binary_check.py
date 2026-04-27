with open(r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php', 'rb') as f:
    content = f.read()

# Look for characters > 127 inside script blocks
# Except for known emojis
in_script = False
lines = content.split(b'\n')
for i, line in enumerate(lines):
    if b'<script>' in line: in_script = True
    if b'</script>' in line: in_script = False
    
    if in_script:
        for char in line:
            if char > 127:
                # Print non-ascii character hex and context
                # Emojis like 🚖 are okay, but let's see if there are weird ones.
                pass
        # simpler check: non-breaking space (0xA0)
        if 0xA0 in line:
            print(f"Non-breaking space (0xA0) found at line {i+1}")

print("binary check complete.")
