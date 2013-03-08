# General

Dashbird is using jQuery 1.8.3, [twitter bootstrap](http://twitter.github.com/bootstrap/) and [SimpleJSLib](https://github.com/roccosportal/SimpleJSLib).

Almost all of the javascript functionality is encapsulated in modules under the namespace `Dashbird`.
Some of them are singletons others can be instanciated.

STILL UNDER DEVELOPMENT

### Models

Some of the modules are a mix between a Model and a Controller. 
For example you have `Dashbird.Post` which holds the data of a post and provides observable properties a view can listen to. On the other hand you also have functions like update and delete which pushes the changes to the server.
A list of modules which follows this scheme.
- Dashbird.Post (contains one Dashbird.Comments)
- Dashbird.Comments (contains multiple Dashbird.Comment)
- Dashbird.Comment

### ViewModels

A view model is a module that illustrates a model
A list of modules which follows this scheme.
- Dashbird.PostHtmlLayer
- Dashbird.CommentsLayer
- Dashbird.CommentLayer
- Dashbird.PostFeedHtmlLayer
- Dashbird.ActivityFeedLayer
- Dashbird.CommentFeedLayer

### View

A view may hold one or more ViewModels and illustrates them on a layer. E.g. a view can just be form as well.
A list of modules which follows this scheme.
- Dashbird.Stack
- Dashbird.SingleView
- Dashbird.Lastest
- Dashbird.Feed
- Dashbird.Notification
- Dashbird.NewPost
- Dashbird.Search

### Controllers

Controllers are the only ones that should interact with the server.
A list of modules which follows this scheme.
- Dashbird.Posts








