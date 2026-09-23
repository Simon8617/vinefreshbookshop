CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin (password = admin123)
INSERT INTO admins (username, password) 
VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "');
