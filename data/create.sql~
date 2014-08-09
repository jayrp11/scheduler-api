create database scheduler;

use scheduler;

create table schedules (
  id int not null auto_increment primary key,
  s_date date not null,
  theme varchar(50) not null,
  is_template boolean not null default false
);

create table resources (
  id int not null auto_increment primary key,
  type varchar(20) not null,
  name varchar(50) not null
);

create table sub_schedules (
  id int not null auto_increment primary key,
  schedule_id int not null,
  start_time timestamp not null,
  end_time timestamp not null,
  title varchar(50),
  presenter varchar(50),
  lead varchar(50), /* this should be replaced by foreign key */

  CONSTRAINT fk_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE ON UPDATE CASCADE
);

create table sub_schedules_resources (
  sub_schedule_id int not null,
  resource_id int not null,

  CONSTRAINT fk_sub_schedule FOREIGN KEY (sub_schedule_id) REFERENCES sub_schedules(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_resource FOREIGN KEY (resource_id) REFERENCES resources(id)
);

create table users (
  id int not null auto_increment primary key,
  username varchar(50),
  password varchar(100)
);