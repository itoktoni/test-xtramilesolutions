import React from 'react';

function UserForm({ formData, onChange, onSubmit, onCancel, editingUser, loading }) {
  return (
    <div className="card shadow">
      <div className="card-body">
        <h2 className="card-title mb-4">
          {editingUser ? 'Edit User' : 'Create New User'}
        </h2>
        <form onSubmit={onSubmit}>
          <div className="mb-3">
            <label htmlFor="name" className="form-label">
              Name:
            </label>
            <input
              type="text"
              className="form-control"
              id="name"
              name="name"
              value={formData.name}
              onChange={onChange}
              placeholder="Enter your name"
              required
            />
          </div>

          <div className="mb-3">
            <label htmlFor="email" className="form-label">
              Email:
            </label>
            <input
              type="email"
              className="form-control"
              id="email"
              name="email"
              value={formData.email}
              onChange={onChange}
              placeholder="Enter your email"
              required
            />
          </div>

          <div className="d-flex gap-2">
            <button
              type="submit"
              className="btn btn-primary flex-grow-1"
              disabled={loading}
            >
              {loading ? 'Saving...' : editingUser ? 'Update User' : 'Create User'}
            </button>
            {editingUser && (
              <button
                type="button"
                className="btn btn-secondary"
                onClick={onCancel}
              >
                Cancel
              </button>
            )}
          </div>
        </form>
      </div>
    </div>
  );
}

export default UserForm;