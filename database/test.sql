use marmot_framework;

CREATE TABLE `pcore_test` (
  `test_id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='测试用表';

ALTER TABLE `pcore_test`
  ADD PRIMARY KEY (`test_id`);
ALTER TABLE `pcore_test`
  MODIFY `test_id` int(10) NOT NULL AUTO_INCREMENT;