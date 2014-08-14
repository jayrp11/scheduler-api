insert into resources(type, name) values('MUSIC', 'Keyboard');
insert into resources(type, name) values('MUSIC', 'Harmonium');
insert into resources(type, name) values('MUSIC', 'Khanzari');
insert into resources(type, name) values('MUSIC', 'Manjira');
insert into resources(type, name) values('MUSIC', 'Ding-dong');
insert into resources(type, name) values('MUSIC', 'Duff');
insert into resources(type, name) values('MUSIC', 'Keyboard 1');
insert into resources(type, name) values('MUSIC', 'Keyboard 2');
insert into resources(type, name) values('MUSIC', 'Koshijoda');
insert into resources(type, name) values('MUSIC', 'Manjira');
insert into resources(type, name) values('MUSIC', 'Octapad');
insert into resources(type, name) values('MUSIC', 'Tabla');
insert into resources(type, name) values('MUSIC', 'Tabla 1');
insert into resources(type, name) values('MUSIC', 'Tabla 2');
insert into resources(type, name) values('MUSIC', 'Guitar');


insert into resources(type, name) values('HALL', 'Main Podium');
insert into resources(type, name) values('HALL', 'Vyaspith');

insert into events(type) values('Ravi Sabha');

insert into orgs(name) values('Sydney - Main Sabha');

insert into users(org_id, username, firstname, lastname, password, authlevel) values(1, "super", "Super - Firstname", "Super - Lastname", "02726d40f378e716981c4321d60ba3a325ed6a4c",0);	
insert into users(org_id, username, firstname, lastname, password, authlevel) values(1, "admin", "Admin - Firstname", "Admin - Lastname", "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8",10);	
insert into users(org_id, username, firstname, lastname, password, authlevel) values(1, "user", "User - Firstname", "User - Lastname", "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8", 50);
insert into users(org_id, username, firstname, lastname, password, authlevel) values(1, "viewer", "Viewer - Firstname", "Viewer - Lastname", "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8", 100);