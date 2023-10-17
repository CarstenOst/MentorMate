create table User
(
    userId    int auto_increment primary key,
    userType  varchar(255)                       not null,
    firstName varchar(255)                       not null,
    lastName  varchar(255)                       not null,
    email     varchar(255)                       not null,
    password  varchar(255)                       not null,
    createdAt datetime default CURRENT_TIMESTAMP not null,
    updatedAt datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    about     text null,
    constraint email
        unique (email)
);

create table Booking
(
    bookingId int auto_increment primary key,
    studentId int null,
    tutorId   int null,
    timestamp datetime                           not null,
    status    varchar(255)                       not null,
    createdAt datetime default CURRENT_TIMESTAMP not null,
    updatedAt datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint Booking_ibfk_1
        foreign key (studentId) references User (userId)
            on delete cascade,
    constraint Booking_ibfk_2
        foreign key (tutorId) references User (userId)
            on delete cascade
);

create index studentId
    on Booking (studentId);

create index tutorId
    on Booking (tutorId);

create table Message
(
    messageId   int auto_increment primary key,
    senderId    int                                not null,
    receiverId  int                                not null,
    timestamp   datetime                           not null,
    messageText text                               not null,
    createdAt   datetime default CURRENT_TIMESTAMP not null,
    constraint Message_ibfk_1
        foreign key (senderId) references User (userId)
            on delete cascade,
    constraint Message_ibfk_2
        foreign key (receiverId) references User (userId)
            on delete cascade
);

create index receiverId
    on Message (receiverId);

create index senderId
    on Message (senderId);

create table Notification
(
    notificationId   int auto_increment primary key,
    receiverId       int                                not null,
    timestamp        datetime                           not null,
    notificationText text                               not null,
    createdAt        datetime default CURRENT_TIMESTAMP not null,
    constraint Notification_ibfk_1
        foreign key (receiverId) references User (userId)
            on delete cascade
);

create index receiverId
    on Notification (receiverId);

