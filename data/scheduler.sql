/* Seed Data */
insert into schedules(s_date, theme) values('2013-10-06', 'Shajanand Charitra');
insert into schedules(s_date, theme) values('2013-10-13', 'Shatriji Maharaj Jivan Charitra');
insert into schedules(s_date, theme) values('2013-10-20', 'Yogi Vani');
insert into schedules(s_date, theme) values('2013-10-27', 'Shanti Shanti Shanti');

insert into sub_schedules(schedule_id, start_time, end_time, title, lead, presenter) 
			values(1, '2013-10-27 04:30:00', '2013-10-27 04:40:00', 'Dhun - 2', 'Test Lead - 2', 'Test Presenter - 2');

select * from schedules;
select * from sub_schedules;
select * from resources;
select * from sub_schedules_resources;

SET FOREIGN_KEY_CHECKS=0; 
truncate schedules;
SET FOREIGN_KEY_CHECKS=1;

truncate schedules;
truncate sub_schedules;

use scheduler;
