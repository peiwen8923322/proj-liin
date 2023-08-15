-- Active: 1666592657434@@127.0.0.1@3306@liin
CREATE TABLE education (  
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號',
    formcode CHAR(10) NOT NULL COMMENT '表單編號',
    formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況',
    creator VARCHAR(20) NOT NULL COMMENT '建立者',
    creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期',
    modifier VARCHAR(20) NOT NULL COMMENT '修改者',
    modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期',
    educode VARCHAR(20) NOT NULL COMMENT '教育程度代碼',
    eduapl VARCHAR(300) NOT NULL COMMENT '教育程度名稱',

    INDEX edu_seq_index(seq),
    INDEX edu_eduapl_index(eduapl),
    PRIMARY KEY (formcode)
) COMMENT '教育程度檔';

INSERT INTO education (formcode, creator, modifier, educode, eduapl) VALUES 
    ('2022100001', '徐培文', '徐培文', 'E001', '研究所以上')
    , ('2022100002', '徐培文', '徐培文', 'E002', '大學/專科')
    , ('2022100003', '徐培文', '徐培文', 'E003', '高中/高職')
    , ('2022100004', '徐培文', '徐培文', 'E004', '國中')
    , ('2022100005', '徐培文', '徐培文', 'E005', '國小')
    , ('2022100006', '徐培文', '徐培文', 'E006', '不識字')
;