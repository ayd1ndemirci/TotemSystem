-- #!sqlite

-- #{ totem
-- #{ createTable

CREATE TABLE IF NOT EXISTS totem(
    playerName VARCHAR(50) NOT NULL,
    totemCount INT(3) DEFAULT 0
);
-- #}

-- # { addPlayer
-- # :playerName string
 INSERT INTO totem(playerName) VALUES (:playerName);
-- # }

-- # { updateTotem
-- # :playerName string
-- # :totemCount int
UPDATE totem SET totemCount = totemCount + :totemCount WHERE playerName = :playerName;
-- # }

-- # { getTotem
-- # :playerName string
 SELECT totemCount FROM totem WHERE playerName = :playerName;
-- # }
-- #}