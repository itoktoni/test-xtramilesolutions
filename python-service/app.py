from flask import Flask, request, jsonify
import json
import os
from datetime import datetime

app = Flask(__name__)

# Data storage directory
USERS_DIR = 'users'

# Ensure directory exists
os.makedirs(USERS_DIR, exist_ok=True)

@app.route('/receive_user', methods=['POST'])
def receive_user():
    try:
        user_data = request.get_json()

        if not user_data or 'name' not in user_data or 'email' not in user_data:
            return jsonify({'error': 'Name and email are required'}), 400

        # Log to console (Docker)
        print(f"[Python Flask] Received user: {user_data['name']} ({user_data['email']})")
        print(f"[Python Flask] Timestamp: {datetime.now().isoformat()}")

        # Save individual user file (1 file 1 record, like Go)
        user_id = user_data.get('id', 0)
        user_filename = os.path.join(USERS_DIR, f"user_{user_id}.json")
        with open(user_filename, 'w') as f:
            json.dump(user_data, f, indent=2)
        print(f"[Python Flask] Saved user to {user_filename}")

        # Check if user name contains "David" (case-insensitive)
        if 'david' in user_data.get('name', '').lower():
            print(f"[Python Flask] David detected!")

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
        users = []
        for filename in os.listdir(USERS_DIR):
            if filename.startswith('user_') and filename.endswith('.json'):
                filepath = os.path.join(USERS_DIR, filename)
                with open(filepath, 'r') as f:
                    users.append(json.load(f))
        return jsonify(users), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/', methods=['GET'])
def health_check():
    return jsonify({'status': 'Python service is running'}), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)