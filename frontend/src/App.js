import React, { useState, useEffect } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import UserForm from './components/UserForm';
import UserTable from './components/UserTable';

function App() {
  const [formData, setFormData] = useState({ name: '', email: '' });
  const [editingUser, setEditingUser] = useState(null);
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(false);

  const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8080';

  const notifySuccess = (message) => toast.success(message, {
    position: 'top-right',
    autoClose: 3000,
    hideProgressBar: false,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
  });

  const notifyError = (message) => toast.error(message, {
    position: 'top-right',
    autoClose: 4000,
    hideProgressBar: false,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
  });

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const fetchUsers = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}/users`);
      if (response.ok) {
        const data = await response.json();
        setUsers(data);
      }
    } catch (err) {
      console.error('Failed to fetch users:', err);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, [API_BASE_URL]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const url = editingUser
      ? `${API_BASE_URL}/users/${editingUser.id}`
      : `${API_BASE_URL}/users`;
    const method = editingUser ? 'PUT' : 'POST';

    try {
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      if (response.ok) {
        notifySuccess(editingUser ? 'User updated successfully!' : 'User created successfully!');
        setFormData({ name: '', email: '' });
        setEditingUser(null);
        fetchUsers();
      } else {
        const errorData = await response.json();
        if (errorData.messages) {
          Object.values(errorData.messages).forEach(messages => {
            messages.forEach(msg => notifyError(msg));
          });
        } else {
          notifyError(errorData.error || 'Failed to save user');
        }
      }
    } catch (err) {
      notifyError('Network error: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (user) => {
    setEditingUser(user);
    setFormData({ name: user.name, email: user.email });
  };

  const handleDelete = async (userId) => {
    if (!window.confirm('Are you sure you want to delete this user?')) {
      return;
    }

    setLoading(true);

    try {
      const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
        method: 'DELETE',
      });

      if (response.ok) {
        notifySuccess('User deleted successfully!');
        fetchUsers();
      } else {
        const errorData = await response.json();
        notifyError(errorData.error || 'Failed to delete user');
      }
    } catch (err) {
      notifyError('Network error: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleCancel = () => {
    setEditingUser(null);
    setFormData({ name: '', email: '' });
  };

  return (
    <div className="min-vh-100 bg-primary">
      <ToastContainer />
      <header className="py-4 text-white text-center">
        <h1 className="mb-4">User Management</h1>
        <div className="container">
          <div className="row justify-content-center">
            <div className="col-md-4 mb-4">
              <UserForm
                formData={formData}
                onChange={handleChange}
                onSubmit={handleSubmit}
                onCancel={handleCancel}
                editingUser={editingUser}
                loading={loading}
              />
            </div>
            <div className="col-md-8">
              <UserTable
                users={users}
                onEdit={handleEdit}
                onDelete={handleDelete}
                loading={loading}
              />
            </div>
          </div>
        </div>
      </header>
    </div>
  );
}

export default App;