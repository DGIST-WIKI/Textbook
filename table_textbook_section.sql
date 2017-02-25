BEGIN;

CREATE TABLE textbook_section(
  -- Primary key
  txbsec_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  -- Title of section
  txbsec_title varbinary(255) NOT NULL,
  -- section number
  txbsec_number INT(11) NOT NULL,
  -- id of page
  txbsec_page INT(11) NOT NULL,
  -- section object (JSON)
  txbsec_section_info mediumblob,
  -- id of textbook
  txbsec_textbook INT UNSIGNED NOT NULL,
  INDEX txbsec_textbook (txbsec_textbook),
  FOREIGN KEY (txbsec_textbook)
      REFERENCES textbook(txb_id)
      ON DELETE CASCADE
)/*$wgDBTableOptions*/;

CREATE INDEX txbsec_page ON textbook_section (txbsec_page);
CREATE INDEX txbsec_title ON textbook_section (txbsec_title);

COMMIT;
