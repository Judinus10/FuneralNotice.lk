
file_path = r'd:\DOWNLOAD_APPS\Xampp\htdocs\FuneralNotice.lk\index.html'
try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    print(f"Line 13 (Index 12): {lines[12].strip()}")
    print(f"Line 2966 (Index 2965): {lines[2965].strip()}")
    
    # Check for other style tags
    style_starts = [i+1 for i, l in enumerate(lines) if '<style>' in l]
    style_ends = [i+1 for i, l in enumerate(lines) if '</style>' in l]
    print(f"Style starts at lines: {style_starts}")
    print(f"Style ends at lines: {style_ends}")

except Exception as e:
    print(f"Error: {e}")
