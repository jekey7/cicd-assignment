CREATE TABLE IF NOT EXISTS member (
  user_id VARCHAR(50) NOT NULL PRIMARY KEY,
  user_pw VARCHAR(255) NOT NULL,
  user_name VARCHAR(100) NOT NULL,
  user_email VARCHAR(255) NOT NULL,
  user_reg_datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO member (user_id, user_pw, user_name, user_email, user_reg_datetime)
VALUES ('admin', 'admin123', 'Admin', 'admin@example.com', NOW())
ON DUPLICATE KEY UPDATE user_id = user_id;
