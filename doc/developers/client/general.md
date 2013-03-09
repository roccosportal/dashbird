# General

Dashbird is using jQuery 1.8.3, [twitter bootstrap](http://twitter.github.com/bootstrap/) and [SimpleJSLib](https://github.com/roccosportal/SimpleJSLib).

Almost all of the javascript functionality is encapsulated in modules under the namespace `Dashbird`.
Some of them are singletons others can be instanciated.

STILL UNDER DEVELOPMENT

### Models

Some of the modules are a mix between a Model and a Controller. 
For example you have `Dashbird.Post` which holds the data of a post and provides observable properties a view can listen to. On the other hand you also have functions like update and delete which pushes the changes to the server.
A list of modules which follows this scheme.
- Dashbird.Models.Post (contains one Dashbird.Comments)
- Dashbird.Models.Comments (contains multiple Dashbird.Comment)
- Dashbird.ModelsComment

### ViewModels

A view model is a module that illustrates a model
A list of modules which follows this scheme.
- Dashbird.ViewModels.Post
- Dashbird.ViewModels.Comments
- Dashbird.ViewModels.Comment
- Dashbird.ViewModels.PostFeed
- Dashbird.ViewModels.ActivityFeed
- Dashbird.ViewModels.CommentFeed

### View

A view may hold one or more ViewModels and illustrates them on a layer. E.g. a view can just be form as well.
A list of modules which follows this scheme.
- Dashbird.Views.Board.Stack
- Dashbird.Views.Board.SingleView
- Dashbird.Views.Board.Lastest
- Dashbird.Views.Board.Feed
- Dashbird.Views.Board.Notification
- Dashbird.Views.Board.NewPost
- Dashbird.Views.Board.Search

### Controllers

Controllers are the only ones that should interact with the server.
A list of modules which follows this scheme.
- Dashbird.Controllers.Posts
- Dashbird.Controllers.User
- Dashbird.Controllers.UserSettings








