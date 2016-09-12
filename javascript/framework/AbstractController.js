var MyFramework;
(function (MyFramework) {
    var AbstractController = (function () {
        function AbstractController() {
        }
        AbstractController.prototype.forwardToView = function (viewName) {
            var fc = MyFramework.FrontController.getInstance();
            fc.renderView(viewName);
        };
        return AbstractController;
    }());
    MyFramework.AbstractController = AbstractController;
})(MyFramework || (MyFramework = {}));
