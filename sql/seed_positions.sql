-- Insert default positions
INSERT INTO positions (name, description) VALUES
('Manager', 'Management position'),
('HR Officer', 'Human Resources Officer'),
('Software Developer', 'Software development role'),
('Receptionist', 'Front desk and reception duties')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;
