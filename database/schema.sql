CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','user') NOT NULL DEFAULT 'user',
  last_login    TIMESTAMP NULL DEFAULT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS comics (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      INT UNSIGNED NOT NULL,
  source_id    INT UNSIGNED NULL,
  title        VARCHAR(150) NOT NULL,
  author       VARCHAR(100) NOT NULL,
  publisher    VARCHAR(100) NULL,
  genre        VARCHAR(50)  NOT NULL DEFAULT 'Other',
  issue_number INT UNSIGNED NULL,
  year         YEAR NULL,
  price        DECIMAL(8,2) NULL,
  status       ENUM('owned','wishlist','reading') NOT NULL DEFAULT 'owned',
  cover        VARCHAR(255) NULL,
  description  TEXT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comics_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_comics_source FOREIGN KEY (source_id) REFERENCES comics(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (id, name, email, password_hash, role) VALUES
(1, 'Admin', 'admin@example.com', '$2b$12$TyH13i.f68PB6U/p.v0aI.fXrkYAvHzigf//0FI/6ufamhPQD9.eS', 'admin');

INSERT INTO comics (user_id, title, author, publisher, genre, issue_number, year, price, status, description) VALUES
(1, 'Amazing Adventures', 'Stan Lee', 'Marvel', 'Superhero', 1, 1962, 12.50, 'owned', 'A classic first issue full of action.'),
(1, 'The Dark Knight Returns', 'Frank Miller', 'DC Comics', 'Superhero', 1, 1986, 18.00, 'owned', 'An aging Batman comes out of retirement.'),
(1, 'Dragon Quest Saga', 'Akira Tora', 'Shonen Press', 'Fantasy', 3, 2015, 9.99, 'reading', 'A young hero sets out to find the lost dragon.'),
(1, 'Space Drifters', 'Mia Torres', 'Indie Orbit', 'Sci-Fi', 7, 2021, 7.25, 'wishlist', 'A crew of misfits travels the outer rim.'),
(1, 'Haunted Hollow', 'Eli Grave', 'Nightfall', 'Horror', 2, 2019, 6.50, 'owned', 'Something is wrong in the small town of Hollow.'),
(1, 'Neon Samurai', 'Ken Mori', 'Shonen Press', 'Manga', 1, 2018, 8.50, 'owned', 'A cyberpunk ronin hunts rogue androids.'),
(1, 'Captain Comet', 'Ray Hollis', 'Starline', 'Superhero', 12, 1998, 5.75, 'owned', 'The cosmic hero faces his greatest foe.'),
(1, 'Moonlit Detective', 'Clara Voss', 'Noir Ink', 'Mystery', 4, 2017, 6.99, 'reading', 'A night-shift detective solves impossible cases.'),
(1, 'Laugh Factory', 'Benny Cole', 'Giggle House', 'Comedy', 9, 2020, 4.99, 'owned', 'Short strips about a very unlucky comic artist.'),
(1, 'Realm of Embers', 'Lyra Quill', 'Pyre Press', 'Fantasy', 1, 2022, 10.50, 'wishlist', 'Dragons, rebels and a stolen crown.'),
(1, 'Orbit Zero', 'Tomas Reyes', 'Indie Orbit', 'Sci-Fi', 2, 2023, 7.99, 'owned', 'The first Mars colony wakes up to a mystery.'),
(1, 'Crypt Keepers', 'Nora Black', 'Nightfall', 'Horror', 5, 2016, 6.25, 'reading', 'Six short tales from the old cemetery.'),
(1, 'Pocket Heroes', 'Mika Sato', 'Shonen Press', 'Manga', 6, 2019, 8.25, 'owned', 'Tiny heroes protect a big city.'),
(1, 'The Last Lighthouse', 'Owen Marsh', 'Tidewater', 'Mystery', 1, 2014, 9.00, 'wishlist', 'A keeper vanishes, leaving only a lantern.'),
(1, 'Turbo Squad', 'Dee Parker', 'Starline', 'Superhero', 8, 2005, 5.50, 'owned', 'Five teens with racing-powered abilities.'),
(1, 'Galaxy Grocers', 'Ivy Chen', 'Giggle House', 'Comedy', 3, 2021, 4.50, 'owned', 'Space-station shopkeepers and their wild customers.'),
(1, 'Shadow of the Wolf', 'Gareth Lowe', 'Pyre Press', 'Fantasy', 7, 2012, 7.50, 'reading', 'A cursed knight guards the northern pass.'),
(1, 'Iron Harbor', 'Sam Ortiz', 'Noir Ink', 'Mystery', 2, 2018, 6.75, 'owned', 'Crime and corruption on the docks.'),
(1, 'Robo Chef', 'Aiko Tanaka', 'Shonen Press', 'Manga', 4, 2020, 7.25, 'wishlist', 'A robot dreams of winning the world cooking cup.'),
(1, 'Skyward Knights', 'Hal Brennan', 'Starline', 'Sci-Fi', 10, 2008, 6.00, 'owned', 'Pilots defend floating cities from sky pirates.');
