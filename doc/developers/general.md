# General

## The model
Ids are at the moment auto increment integers but due future distributed servers the will be unique universal identifiers.
The model on the database may be look different but follows the same definition.


### User
Represents a user with a `Name`, `Password` (BlowFish salted) and a unique `UserId`.

### UserShare
Represents the connection between two users in one direction. You can share posts with people you are connected to.
`UserId` is the identifier of the user that is connected to another user via `ConnectedUserId`. The other user must have the same entry with switched `UserId` and `ConnectedUserId` to be able to share with him. `UserShareId` is the unique identifer of an entry.

### Post
Represents a post that contains a `Text` and was created (Datetime `Created`) by a User (`UserId`). A post is updated (Datetime `Updated`) when the `Text` of the posts were changed, a new comment was added, was deleted, post tags changed or post shares were edited. `PostId` is the unique identifer of an entry. Only the post owner can edit (`Text`) or delete the post. `LastView` contains a datetime or null which represents the last time the current user has seen the post. The current user can only edit his last view for the post. 

### PostShare 
Defines with which users (`UserId`) the post (`PostId`) is shared. `PostShareId` is the unique identifer of an entry. Only Users which are associated in UserShares can be added to post shares. Only the post owner can set post shares.

### Comment
A user (`UserId`) can add a `Text` to a post (`PostId`). The create datetime is saved in `DateTime`. Only Users which are associated in post shares with a post can add a comment. Nobody can edit the comment, only the comment owner can delete the comment. `CommentId` is the unique identifer of an entry.

### Tag 
Represents a tag with a `Title` and is identified by `TagId`. Everyone can create tags but there never exists Tags with duplicates titles.

### PostTag
Represents a tag (`TagId`) that was associated with a post (`PostId`). `PostTagId` is the unique identifer of an entry. Only the post owner can edit tags for a post.



