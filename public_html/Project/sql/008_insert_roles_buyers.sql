INSERT INTO Roles(id, name, is_active) VALUES(1, 'Client', 1) ON DUPLICATE KEY UPDATE name = name;
