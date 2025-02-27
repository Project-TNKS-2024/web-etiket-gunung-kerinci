import json
from collections import defaultdict

def find_duplicate_countries(filename):
    try:
        # Buka dan baca file JSON
        with open(filename, "r", encoding="utf-8") as file:
            countries = json.load(file)

        # Kelompokkan berdasarkan 'code'
        grouped_countries = defaultdict(list)
        for country in countries:
            code = country.get("code")
            if code:
                grouped_countries[code].append(country)

        # Filter hanya kode yang memiliki lebih dari 1 data
        duplicates = {code: data for code, data in grouped_countries.items() if len(data) > 1}

        return duplicates

    except FileNotFoundError:
        return "File not found!"
    except json.JSONDecodeError:
        return "Invalid JSON format!"

# Contoh penggunaan
if __name__ == "__main__":
    filename = "negara.json"
    result = find_duplicate_countries(filename)
    print(json.dumps(result, indent=4, ensure_ascii=False))  # Print hasil dalam format JSON yang rapi
