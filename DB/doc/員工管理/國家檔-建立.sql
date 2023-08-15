-- Active: 1666592657434@@127.0.0.1@3306@liin
CREATE TABLE contries (  
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號',
    formcode CHAR(10) NOT NULL COMMENT '表單編號',
    formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況',
    creator VARCHAR(20) NOT NULL COMMENT '建立者',
    creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期',
    modifier VARCHAR(20) NOT NULL COMMENT '修改者',
    modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期',
    ctycode VARCHAR(20) NOT NULL COMMENT '國家代碼',
    ctyapl VARCHAR(300) NOT NULL COMMENT '國家名稱',

    INDEX cty_seq_index(seq),
    INDEX cty_ctyapl_index(ctyapl),
    PRIMARY KEY (formcode)
) COMMENT '國家檔';

INSERT INTO education (formcode, creator, modifier, ctycode, ctyapl) VALUES 
    ('2022100001', '徐培文', '徐培文', 'C001', '台灣')
    , ('2022100002', '徐培文', '徐培文', 'C002', '印尼')
    , ('2022100003', '徐培文', '徐培文', 'C003', '越南')
    , ('2022100004', '徐培文', '徐培文', 'C004', '中國')
;