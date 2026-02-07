from flask import Flask, request, jsonify
import json
import os
from datetime import datetime

app = Flask(__name__)

# Data storage files
DATA_FILE = 'received_users.json'
DAVID_FILE = 'david_users.json'
USERS_DIR = 'users'

# Ensure directories exist
os.makedirs(USERS_DIR, exist_ok=True)

# Ensure data files exist
if not os.path.exists(DATA_FILE):
    with open(DATA_FILE, 'w') as f:
        json.dump([], f)

if not os.path.exists(DAVID_FILE):
    with open(DAVID_FILE, 'w') as f:
        json.dump([], f)

@app.route('/receive_user', methods=['POST'])
def receive_user():
    try:
        user_data = request.get_json()

        if not user_data or 'name' not in user_data or 'email' not in user_data:
            return jsonify({'error': 'Name and email are required'}), 400

        # Log to console (Docker)
        print(f"[Python Flask] Received user: {user_data['name']} ({user_data['email']})")
        print(f"[Python Flask] Timestamp: {datetime.now().isoformat()}")

        # Load existing data
        with open(DATA_FILE, 'r') as f:
            users = json.load(f)

        # Append new user
        users.append(user_data)

        # Save updated data
        with open(DATA_FILE, 'w') as f:
            json.dump(users, f, indent=2)

        # Check if user name contains "David" (case-insensitive)
        if 'david' in user_data.get('name', '').lower():
            print(f"[Python Flask] David detected! Saving to users/ folder (1 file per user)")

            # Save individual David user file (1 file 1 record, like Go)
            david_id = user_data.get('id', 0)
            david_filename = os.path.join(USERS_DIR, f"user_{david_id}.json")
            with open(david_filename, 'w') as f:
                json.dump(user_data, f, indent=2)
            print(f"[Python Flask] David user saved to {david_filename}")

            # Also update david_users.json for reference
            with open(DAVID_FILE, 'r') as f:
                david_users = json.load(f)
            david_users.append(user_data)
            with open(DAVID_FILE, 'w') as f:
                json.dump(david_users, f, indent=2)
            print(f"[Python Flask] Total David users: {len(david_users)}")

        return jsonify({
            'message': 'User received and stored successfully',
            'user': user_data
        }), 200

    except Exception as e:
        print(f"[Python Flask] Error: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/users', methods=['GET'])
def get_users():
    try:
        with open(DATA_FILE, 'r') as f:
            users = json.load(f)

        return jsonify(users), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/', methods=['GET'])
def health_check():
    return jsonify({'status': 'Python service is running'}), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)