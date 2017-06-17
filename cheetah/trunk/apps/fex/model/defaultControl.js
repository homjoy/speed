function defaultAction(php) {
    if (!php) {
        console.log('php not assign ' + this.req.url);
        return
    }
    var mSelf = this;
    this.eventHandle.onOver = function (data) {
    };
}


exports.bind = defaultAction;
