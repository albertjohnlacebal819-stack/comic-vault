ALTER TABLE comics ADD COLUMN source_id INT UNSIGNED NULL AFTER user_id;

ALTER TABLE comics ADD CONSTRAINT fk_comics_source FOREIGN KEY (source_id) REFERENCES comics(id) ON DELETE SET NULL;

UPDATE comics c
JOIN users uc ON uc.id = c.user_id AND uc.role = 'user'
JOIN comics a ON a.title = c.title AND a.author = c.author AND a.issue_number <=> c.issue_number
JOIN users ua ON ua.id = a.user_id AND ua.role = 'admin'
SET c.source_id = a.id, c.cover = NULL;
