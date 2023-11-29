create table User
(
    userId    int unsigned auto_increment primary key,
    userType  varchar(255)                       not null,
    firstName varchar(255)                       not null,
    lastName  varchar(255)                       not null,
    email     varchar(255)                       not null,
    password  varchar(255)                       not null, -- hashed using bcrypt
    createdAt datetime default CURRENT_TIMESTAMP not null,
    updatedAt datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    about     text                               null,
    constraint email
        unique (email)
);

create table Booking
(
    bookingId   int unsigned auto_increment primary key,
    studentId   int unsigned                       null,
    tutorId     int unsigned                       not null, -- modified constraint to 'not null'
    bookingTime datetime                           not null,
    location    varchar(255)                       not null, -- renamed from 'status'
    createdAt   datetime default CURRENT_TIMESTAMP not null,
    updatedAt   datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
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
    messageId   int unsigned auto_increment primary key,
    senderId    int unsigned                       not null,
    receiverId  int unsigned                       not null,
    sentAt      datetime default CURRENT_TIMESTAMP not null, -- renamed from 'timestamp'
    messageText text                               not null,
    isRead      boolean  default false             not null, -- added 'isRead'
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
    notificationId   int unsigned auto_increment primary key,
    receiverId       int unsigned                       not null,
    sentAt           datetime default CURRENT_TIMESTAMP not null, -- renamed from 'timestamp'
    notificationText text                               not null,
    isRead           boolean  default false             not null, -- added 'isRead'
    constraint Notification_ibfk_1
        foreign key (receiverId) references User (userId)
            on delete cascade
);

create index receiverId
    on Notification (receiverId);
