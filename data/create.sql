create database scheduler;

use scheduler;

create table events (
  id int not null auto_increment primary key,
  type varchar(20) not null
);

create table schedules (
  id int not null auto_increment primary key,
  event_id int not null default 1,
  s_date date not null,
  theme varchar(50) not null,
  locked boolean not null default false,

  CONSTRAINT fk_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE  
);

create table resources (
  id int not null auto_increment primary key,
  type varchar(20) not null,
  name varchar(50) not null
);

create table sub_schedules (
  id int not null auto_increment primary key,
  schedule_id int not null,
  start_time time not null,
  end_time time not null,
  title varchar(50) not null,
  notes varchar(200),
  presenter varchar(50),
  lead varchar(50),

  CONSTRAINT fk_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE ON UPDATE CASCADE
);

create table sub_schedules_resources (
  sub_schedule_id int not null,
  resource_id int not null,

  CONSTRAINT fk_sub_schedule FOREIGN KEY (sub_schedule_id) REFERENCES sub_schedules(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_resource FOREIGN KEY (resource_id) REFERENCES resources(id)
);

create table orgs (
  id int not null auto_increment primary key,
  name varchar(50) not null
);

create table users (
  id int not null auto_increment primary key,
  org_id int not null,
  username varchar(50) not null,
  password varchar(100) not null,
  firstname varchar(100) not null,
  lastname varchar(100) not null,
  authlevel int not null,

  CONSTRAINT fk_org FOREIGN KEY (org_id) REFERENCES orgs(id)
);