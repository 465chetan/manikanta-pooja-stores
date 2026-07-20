import glob

replacements = {
    'ðŸ•‰ï¸ ': '🕉️',
    'â€“': '–',
    'â€”': '—',
    'â­ ': '⭐',
    'ðŸ†•': '🆕',
    'ðŸ ·ï¸ ': '🏷️',
    'âœ ï¸ ': '📝',
    'â˜…': '★',
    'ðŸ™ ': '🙏',
    'ðŸŽ‰': '🎉',
    'ðŸŒ ': '🌍',
    'ðŸ’š': '💚',
    '&rdquo;¢': '•',
    '&rdquo;¦': '...',
    'ðŸ›’': '🛒',
    'ðŸ’³': '💳',
    'âœ…': '✅',
    'ðŸšš': '🚚',
    'âš¡': '⚡',
    'â€¢': '•',
    'ðŸŒ¿': '🌿',
    'ðŸ º': '🛕',
    'ðŸ¤ ': '🤝',
    'ðŸ’°': '💰',
    'ðŸ“š': '📚',
    'ðŸ›•': '🛕',
    'ðŸ“ ': '📍',
    'ðŸ“¦': '📦',
    'â‚¹': '₹',
    'ðŸ’¬': '💬',
    'ðŸ”„': '🔄',
    'ðŸ‘¤': '👤',
    'ðŸ“²': '📲',
    'ðŸ“¢': '📢',
    'â€"': '—',
    'â€': '—'
}

files = glob.glob('*.html') + glob.glob('admin/*.html')
count = 0
for f_path in files:
    try:
        with open(f_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        orig_content = content
        for bad, good in replacements.items():
            content = content.replace(bad, good)
            
        if content != orig_content:
            with open(f_path, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f'Fixed mojibake in {f_path}')
            count += 1
    except Exception as e:
        print(f"Error on {f_path}: {e}")
        
print(f'Total files fixed: {count}')
