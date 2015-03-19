DROP DATABASE jomon;

CREATE DATABASE jomon;

USE jomon;


#
# table 'MEMBERS'
#
#DROP TABLE jomon_members;

CREATE TABLE jomon_members (
  member_id int(11) unsigned NOT NULL auto_increment,
  firstname varchar(100) default NULL,
  lastname varchar(100) default NULL,
  login varchar(100) NOT NULL default '',
  passwd varchar(32) NOT NULL default '',
  email varchar(60),
  imageid int NULL,
  PRIMARY KEY  (member_id)
) TYPE=MyISAM;

insert into jomon_members (member_id, firstname, lastname, login, passwd, email) VALUES(1, "System", "Manager", "admin", "21232f297a57a5a743894a0e4a801fc3", "kevin.hilton@prodigyworks.co.uk");
insert into jomon_members (member_id, firstname, lastname, login, passwd, email) VALUES(2, "Customer", "Demonstration", "customer", "d3dde2723247d8d5fc3f76dceb3d4324", "kevin.hilton@prodigyworks.co.uk");
insert into jomon_members (member_id, firstname, lastname, login, passwd, email) VALUES(3, "Contractor", "Demonstration", "contractor", "d3dde2723247d8d5fc3f76dceb3d4324", "kevin.hilton@prodigyworks.co.uk");
insert into jomon_members (member_id, firstname, lastname, login, passwd, email) VALUES(4, "Operations", "Demonstration", "operator", "d3dde2723247d8d5fc3f76dceb3d4324", "kevin.hilton@prodigyworks.co.uk");

# 
# table: 'PAGES'
#
#DROP TABLE jomon_pages;

CREATE TABLE jomon_pages (
  pageid  int(11) unsigned NOT NULL auto_increment,
  pagename varchar(30),
  label varchar(30) DEFAULT NULL,
  PRIMARY KEY  (pageid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE UNIQUE INDEX ix_page ON jomon_pages(pagename);

insert into jomon_pages (pageid, pagename, label) values (1, 'index.php', 'Home');
insert into jomon_pages (pageid, pagename, label) values (2, 'system-access-denied.php', 'Access Denied');
insert into jomon_pages (pageid, pagename, label) values (3, 'system-admin.php', 'Admin');
insert into jomon_pages (pageid, pagename, label) values (5, 'system-login-timeout.php', 'Session Timeout');
insert into jomon_pages (pageid, pagename, label) values (6, 'system-login-failed.php', 'Login Failed');
insert into jomon_pages (pageid, pagename, label) values (8, 'system-register.php', 'Register');
insert into jomon_pages (pageid, pagename, label) values (10, 'system-register-success.php', 'Register Success');
insert into jomon_pages (pageid, pagename, label) values (11, 'system-admin-roles.php', 'Roles');
insert into jomon_pages (pageid, pagename, label) values (13, 'system-register-exec.php', 'Register Save');
insert into jomon_pages (pageid, pagename, label) values (14, 'system-imageviewer.php', 'Image Viewer');

# 
# table: 'PAGESROLES'
#
#DROP TABLE jomon_pageroles;

CREATE TABLE jomon_pageroles (
  pageroleid int(11) unsigned NOT NULL auto_increment,
  pageid int not null,
  roleid VARCHAR(20) not null,
  PRIMARY KEY  (pageroleid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE UNIQUE INDEX ix_pageroles ON jomon_pageroles(pageid, roleid);

insert into jomon_pageroles (pageid, roleid) values (1, 'PUBLIC');
insert into jomon_pageroles (pageid, roleid) values (2, 'PUBLIC');
insert into jomon_pageroles (pageid, roleid) values (3, 'ADMIN');
insert into jomon_pageroles (pageid, roleid) values (5, 'PUBLIC');
insert into jomon_pageroles (pageid, roleid) values (6, 'PUBLIC');
insert into jomon_pageroles (pageid, roleid) values (8, 'PUBLIC');
insert into jomon_pageroles (pageid, roleid) values (10, 'PUBLIC');
insert into jomon_pageroles (pageid, roleid) values (11, 'ADMIN');
insert into jomon_pageroles (pageid, roleid) values (13, 'ADMIN');
insert into jomon_pageroles (pageid, roleid) values (14, 'PUBLIC');

# 
# table: 'PAGENAVIGATION'
#
#DROP TABLE jomon_pagenavigation;

CREATE TABLE jomon_pagenavigation (
  pagenavigationid int(11) unsigned NOT NULL auto_increment,
  pageid int not null,
  childpageid int not null,
  sequence int not null,
  pagetype varchar(1),
  title nvarchar(100),
  PRIMARY KEY  (pagenavigationid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE UNIQUE INDEX ix_pagenav ON jomon_pagenavigation(pageid, childpageid, sequence);

insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 1, 1, 'P');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype, title) values (1, 8, 300, 'M', 'Administration');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 11, 500, 'M');

# 
# table: 'ROLES'
#
#DROP TABLE jomon_roles;

CREATE TABLE jomon_roles (
  roleid varchar(20) DEFAULT NULL,
  PRIMARY KEY  (roleid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert into jomon_roles (roleid) values ('PUBLIC');
insert into jomon_roles (roleid) values ('ADMIN');
insert into jomon_roles (roleid) values ('USER');
insert into jomon_roles (roleid) values ('RECRUITER');
insert into jomon_roles (roleid) values ('CONTRACTOR');
insert into jomon_roles (roleid) values ('OPERATIONS');

# 
# table: 'USERROLES'
#
#DROP TABLE jomon_userroles;

CREATE TABLE jomon_userroles (
  userroleid int(11) unsigned NOT NULL auto_increment,
  roleid varchar(20) DEFAULT NULL,
  memberid int(11) DEFAULT NULL,
  PRIMARY KEY  (userroleid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE UNIQUE INDEX ix_userroles ON jomon_userroles(roleid, memberid);

insert into jomon_userroles (roleid, memberid) values ('PUBLIC', 1);
insert into jomon_userroles (roleid, memberid) values ('ADMIN', 1);
insert into jomon_userroles (roleid, memberid) values ('USER', 1);
insert into jomon_userroles (roleid, memberid) values ('OPERATIONS', 1);
insert into jomon_userroles (roleid, memberid) values ('RECRUITER', 2);
insert into jomon_userroles (roleid, memberid) values ('PUBLIC', 2);
insert into jomon_userroles (roleid, memberid) values ('USER', 2);
insert into jomon_userroles (roleid, memberid) values ('CONTRACTOR', 3);
insert into jomon_userroles (roleid, memberid) values ('PUBLIC', 3);
insert into jomon_userroles (roleid, memberid) values ('USER', 3);
insert into jomon_userroles (roleid, memberid) values ('OPERATIONS', 4);
insert into jomon_userroles (roleid, memberid) values ('PUBLIC', 4);
insert into jomon_userroles (roleid, memberid) values ('USER', 4);



#DROP TABLE jomon_images;

CREATE TABLE jomon_images (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    path CHAR(255) DEFAULT '',
    mimetype CHAR(50) DEFAULT '',
    name CHAR(255) DEFAULT '',
    imgwidth SMALLINT(4) DEFAULT 0,
    imgheight SMALLINT(4) DEFAULT 0,
    tag CHAR(255) DEFAULT '',
    description CHAR(255) DEFAULT '',
    image LONGBLOB NULL,
    createddate TIMESTAMP(14) NULL,
    PRIMARY KEY (id), KEY ID(id), 
   FULLTEXT KEY search_index(name, description)) 
TYPE=MyISAM; 


#DROP TABLE jomon_messages;

CREATE TABLE jomon_messages (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    from_member_id int,
    to_member_id int,
    message text,
    createddate TIMESTAMP(14) NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



insert into jomon_pages (pageid, pagename, label) values (100, 'profile.php', 'My Profile');
insert into jomon_pages (pageid, pagename, label) values (110, 'customer-login.php', 'Customer');
insert into jomon_pages (pageid, pagename, label) values (120, 'contractor-login.php', 'Contractor');
insert into jomon_pages (pageid, pagename, label) values (125, 'operations-login.php', 'Operations');
insert into jomon_pages (pageid, pagename, label) values (130, 'users.php', 'Manage Users');

insert into jomon_pageroles (pageid, roleid) values (100, 'USER');
insert into jomon_pageroles (pageid, roleid) values (110, 'UNAUTHENTICATED');
insert into jomon_pageroles (pageid, roleid) values (120, 'UNAUTHENTICATED');
insert into jomon_pageroles (pageid, roleid) values (125, 'UNAUTHENTICATED');
insert into jomon_pageroles (pageid, roleid) values (130, 'ADMIN');

insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 100, 100, 'P');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 110, 110, 'P');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 120, 120, 'P');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 125, 105, 'P');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 130, 630, 'M');


ALTER TABLE jomon_pagenavigation add (
	divider int NULL
);

CREATE TABLE jomon_documents (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	headerid int, 
	name varchar(255), 
	filename varchar(255), 
	mimetype varchar(255),
	size int,
    image LONGBLOB NULL,
    createdby int,
    createddate TIMESTAMP(14) NULL,
    lastmodifiedby int,
    lastmodifieddate TIMESTAMP(14) NULL,
    sessionid varchar(50) NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



insert into jomon_pages (pageid, pagename, label) values (2000, 'system-login.php', 'Account log in');
insert into jomon_pageroles (pageid, roleid) values (2000, 'PUBLIC');

ALTER TABLE jomon_roles add (
	systemrole varchar(1) NULL
);

UPDATE jomon_roles SET systemrole = 'N' where systemrole is NULL;
UPDATE jomon_roles SET systemrole = 'Y' where roleid IN ('PUBLIC', 'USER');

ALTER TABLE jomon_members add (
	systemuser varchar(1) NULL
);

UPDATE jomon_members SET systemuser = 'N' where systemuser is NULL;
UPDATE jomon_members SET systemuser = 'Y' where member_id = 1;


insert into jomon_pages (pageid, pagename, label) values (140, 'customer.php', 'Customer Portal');
insert into jomon_pages (pageid, pagename, label) values (150, 'customersites.php', 'Customer Premises');
insert into jomon_pages (pageid, pagename, label) values (160, 'customersitecontacts.php', 'Premises Contacts');
insert into jomon_pages (pageid, pagename, label) values (170, 'raiseworkrequest.php', 'Raise Work Request');
insert into jomon_pages (pageid, pagename, label) values (180, 'schedulejob.php', 'Schedule Job');
insert into jomon_pages (pageid, pagename, label) values (190, 'calendar.php', 'Schedule Calendar');
insert into jomon_pages (pageid, pagename, label) values (200, 'listcurrentjobs.php', 'List Current Jobs');
insert into jomon_pages (pageid, pagename, label) values (210, 'listpastjobs.php', 'List Past Jobs');
insert into jomon_pages (pageid, pagename, label) values (220, 'listworkhistory.php', 'List Work History');
insert into jomon_pages (pageid, pagename, label) values (230, 'listoutstandinginvoice.php', 'List Outstanding Invoices');
insert into jomon_pages (pageid, pagename, label) values (240, 'signoffjob.php', 'Sign Off Job');
insert into jomon_pages (pageid, pagename, label) values (250, 'ratejob.php', 'Rate Job');

insert into jomon_pageroles (pageid, roleid) values (140, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (150, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (160, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (170, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (180, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (190, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (200, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (210, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (220, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (230, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (240, 'RECRUITER');
insert into jomon_pageroles (pageid, roleid) values (250, 'RECRUITER');

insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype, title ) values (1, 150, 200, 'M', 'Configuration');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 160, 210, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype, title) values (1, 170, 220, 'M', 'Work');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 180, 230, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 190, 240, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype, title) values (1, 200, 250, 'M', 'Reports');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 210, 260, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 220, 270, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 230, 280, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 240, 241, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 250, 242, 'M');



insert into jomon_pages (pageid, pagename, label) values (1140, 'contractor.php', 'Contractor Portal');
insert into jomon_pages (pageid, pagename, label) values (1150, 'ctraiseworkrequest.php', 'Raise Work Request');
insert into jomon_pages (pageid, pagename, label) values (1160, 'raiseinvoice.php', 'Raise Invoice');

insert into jomon_pageroles (pageid, roleid) values (1140, 'CONTRACTOR');
insert into jomon_pageroles (pageid, roleid) values (1150, 'CONTRACTOR');
insert into jomon_pageroles (pageid, roleid) values (1160, 'CONTRACTOR');

insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype, title ) values (1, 1150, 200, 'M', 'Work');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 1160, 210, 'M');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 1170, 220, 'M');



insert into jomon_messages (from_member_id, to_member_id, message, createddate) values (1, 2, 'Welcome to CC70 PM Portal prototype', NOW());
insert into jomon_messages (from_member_id, to_member_id, message, createddate) values (1, 3, 'Welcome to CC70 PM Portal prototype', NOW());

CREATE TABLE jomon_questioncategory (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	name varchar(255) not null, 
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE unique index ix_questioncategory ON jomon_questioncategory (name);

CREATE TABLE jomon_topic (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    childid int not null,
	name varchar(255), 
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE unique index ix_topic ON jomon_topic (id, childid);

CREATE TABLE jomon_question (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    memberid int,
	title varchar(255), 
	body text, 
	tags text, 
	topic int, 
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


insert into jomon_pages (pageid, pagename, label) values (1250, 'articles.php', 'Articles');
insert into jomon_pageroles (pageid, roleid) values (1250, 'PUBLIC');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1, 1250, 200, 'P');
insert into jomon_pages (pageid, pagename, label) values (1251, 'writearticle.php', 'Ask Question');
insert into jomon_pageroles (pageid, roleid) values (1251, 'PUBLIC');
insert into jomon_pagenavigation (pageid, childpageid, sequence, pagetype) values (1250, 1251, 200, 'M');

insert into jomon_questioncategory (name) values ('General');
insert into jomon_questioncategory (name) values ('FAQs');
insert into jomon_questioncategory (name) values ('Tutorial');
insert into jomon_questioncategory (name) values ('Review');
insert into jomon_questioncategory (name) values ('Resource');
insert into jomon_questioncategory (name) values ('Best Practices');


CREATE TABLE jomon_chatsession (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    requestmemberid int,
    responsememberid int,
    requestsessionid varchar(60),
    responsesessionid varchar(60),
	createddate timestamp, 
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE jomon_chatmessages (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    chatsessionid int,
    memberid int,
    message text,
    createddate TIMESTAMP NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert into jomon_pages (pageid, pagename, label) values (1252, 'requestexpert.php', 'Request Expert');
insert into jomon_pageroles (pageid, roleid) values (1252, 'PUBLIC');
insert into jomon_pages (pageid, pagename, label) values (1253, 'answerexpertrequest.php', 'Expert Response');
insert into jomon_pageroles (pageid, roleid) values (1253, 'PUBLIC');
