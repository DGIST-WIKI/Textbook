BEGIN;

CREATE TABLE textbook(
  -- Primary key
  txb_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  -- Id of page
  txb_page INT UNSIGNED NOT NULL,
  -- Title of textbook
  txb_title varbinary(255) NOT NULL,
  -- author of textbook
  txb_author varbinary(255)
)/*$wgDBTableOptions*/;

CREATE UNIQUE INDEX txb_page ON textbook (txb_page);

CREATE INDEX txb_title ON textbook (txb_title);

COMMIT;
