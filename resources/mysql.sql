-- #!mysql
-- #{records

-- # { init
CREATE TABLE IF NOT EXISTS records
(
    pos           INT,
    type          VARCHAR(25),
    name          VARCHAR(80),
    value         BIGINT,
    server        VARCHAR(20),
    season        INT,
    unit          VARCHAR(20),
    extra         JSON,
    PRIMARY KEY(pos, type, server)
);
-- # }

-- # { select
-- #    :type string
-- #    :server string
SELECT * FROM records
WHERE type=:type AND server=:server;
-- # }

-- # { select_no_server
-- #    :type string
SELECT * FROM records
WHERE type=:type ORDER BY value DESC LIMIT 10;
-- # }

-- # }