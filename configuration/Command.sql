SELECT * FROM users;
DELETE FROM users WHERE user_id BETWEEN 1 AND 9;


SELECT setval(pg_get_serial_sequence('users', 'user_id'), MAX(user_id)) FROM users;
