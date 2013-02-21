Dashbird.DrawingManager =  SimpleJSLib.BaseObject.inherit(function(me, _protected){
	_protected.TRIGGER_REDRAW_DELAY = 50;

	_protected.willTriggerRedraw = false

	_protected.drawingChangeSet = null;

	_protected.redraw = null;

	_protected.isAllowedToRedraw = null;

	_protected.defaultChangeSetPropertyNames = null;

	// constructor
	// @var parameters (.construct(<function>, <function>, [])) 
	// [0] the redraw function that gets called when a redraw is triggerd
	// [1] the function that return if the parent is allowed to redraw
	// [2] an array of the default change set property names, like ['text', 'username']
	_protected.construct = function(parameters){
		_protected.redraw = parameters[0];
		_protected.isAllowedToRedraw = parameters[1];
		_protected.defaultChangeSetPropertyNames = parameters[2];
		me.setDrawingChangeSetToDefault();
	}

	me.setDrawingChangeSetToDefault = function(){
		_protected.drawingChangeSet = {};
		// setting to default
		for (var i = 0; i < _protected.defaultChangeSetPropertyNames.length; i++) {
			_protected.drawingChangeSet[_protected.defaultChangeSetPropertyNames[i]] = false;
		};
	}

	me.queueRedraw = function(changes){
		if(_protected.isAllowedToRedraw() && !_protected.willTriggerRedraw){
			_protected.willTriggerRedraw = true;
			// trigger with delay
			// if multiple changes are comming up
			// we hopefull just send one redrawing event
			setTimeout(function(){
				_protected.redraw();
				_protected.willTriggerRedraw = false;
			}, _protected.TRIGGER_REDRAW_DELAY)
		}

		for (var i = 0; i < changes.length; i++) {
			_protected.drawingChangeSet[changes[i]] = true;
		};
	}

	me.getDrawingChangeSet = function(){
		return _protected.drawingChangeSet;
	}

	return me;
});