-- Active: 1666592657434@@127.0.0.1@3306@liin
--SELECT * from employees;
--SELECT seq, LENGTH(seq) AS 'seq_length', CASE WHEN LENGTH(seq) = 1 THEN CONCAT('202210', REPEAT(4-LENGTH(seq), '0'), seq) END AS 'formcode' from employees;
--SELECT seq, LENGTH(seq) AS 'seq_length', CONCAT('202210', REPEAT('0', 3-LENGTH(seq)), seq) AS 'formcode' from employees; --建立表單編號
--UPDATE employees SET formcode = CONCAT('202210', REPEAT('0', 3-LENGTH(seq)), seq); --建立表單編號