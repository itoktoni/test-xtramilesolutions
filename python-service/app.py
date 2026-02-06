from flask import Flask, request, jsonify
import json
import os

app = Flask(__name__)

# Data storage file
DATA_FILE = 'received_users.json'

# Ensure data file exists
if not os.path.exists(DATA_FILE):
    with open(DATA_FILE, 'w') as f:
        json.dump([], f)

@app.route('/receive_user', methods=['POST'])
def receive_user():
    try:
        user_data = request.get_json()

        if not user_data or 'name' not in user_data or 'email' not in user_data:
            return jsonify({'error': 'Name and email are required'}), 400

        # Load existing data
        with open(DATA_FILE, 'r') as f:
            users = json.load(f)

        # Append new user
        users.append(user_data)

        # Save updated data
        with open(DATA_FILE, 'w') as f:
            json.dump(users, f, indent=2)

        return jsonify({
            'message': 'User received and stored successfully',
            'user': user_data
        }), 200

    except Exception as e:
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