-- MySQL schema for Quiz Arena
CREATE DATABASE IF NOT EXISTS `quiz_arena` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quiz_arena`;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('science','sports','gk','video_games') NOT NULL,
  difficulty ENUM('easy','medium','hard','extreme') NOT NULL,
  question_text TEXT NOT NULL,
  option_a VARCHAR(255) NOT NULL,
  option_b VARCHAR(255) NOT NULL,
  option_c VARCHAR(255) NOT NULL,
  option_d VARCHAR(255) NOT NULL,
  correct_option ENUM('A','B','C','D') NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS matches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  status ENUM('queued','active','completed') NOT NULL DEFAULT 'queued',
  category ENUM('science','sports','gk','video_games') NOT NULL,
  difficulty ENUM('easy','medium','hard','extreme') NOT NULL,
  player1_id INT NOT NULL,
  player2_id INT DEFAULT NULL,
  winner_id INT DEFAULT NULL,
  started_at DATETIME DEFAULT NULL,
  ended_at DATETIME DEFAULT NULL,
  FOREIGN KEY (player1_id) REFERENCES users(id),
  FOREIGN KEY (player2_id) REFERENCES users(id),
  FOREIGN KEY (winner_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS match_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  match_id INT NOT NULL,
  question_id INT NOT NULL,
  question_index INT NOT NULL,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  match_id INT NOT NULL,
  user_id INT NOT NULL,
  question_id INT NOT NULL,
  answer ENUM('A','B','C','D') NOT NULL,
  time_ms INT NOT NULL,
  is_correct TINYINT(1) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB;

-- Leaderboard materialized by aggregation at runtime

-- Helpful indexes
ALTER TABLE questions
  ADD INDEX idx_questions_cat_diff (category, difficulty);

ALTER TABLE matches
  ADD INDEX idx_matches_status_cat_diff (status, category, difficulty);

ALTER TABLE match_questions
  ADD UNIQUE INDEX idx_match_questions_order (match_id, question_index);

ALTER TABLE answers
  ADD INDEX idx_answers_match_question (match_id, question_id),
  ADD UNIQUE INDEX idx_answers_unique (match_id, user_id, question_id);
