import React from 'react';

function UserTable({ users, onEdit, onDelete, loading }) {
  return (
    <div className="card shadow">
      <div className="card-body">
        <h2 className="card-title mb-4">
          All Users ({users.length})
        </h2>
        {users.length === 0 ? (
          <p className="text-muted text-center py-4">
            No users found. Create one above!
          </p>
        ) : (
          <div className="table-responsive">
            <table className="table table-striped table-hover">
              <thead className="table-primary">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {users.map((user) => (
                  <tr key={user.id}>
                    <td>{user.id}</td>
                    <td>{user.name}</td>
                    <td>{user.email}</td>
                    <td>
                      <div className="d-flex gap-2">
                        <button
                          className="btn btn-sm btn-primary"
                          onClick={() => onEdit(user)}
                          disabled={loading}
                        >
                          Edit
                        </button>
                        <button
                          className="btn btn-sm btn-danger"
                          onClick={() => onDelete(user.id)}
                          disabled={loading}
                        >
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}

export default UserTable;