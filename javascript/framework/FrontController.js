var MyFramework;
(function (MyFramework) {
    var FrontController = (function () {
        function FrontController() {
        }
        Object.defineProperty(FrontController.prototype, "Instance", {
            get: function () {
                if (this.instance == null || this.instance == undefined) {
                    this.instance = new FrontContoller();
                }
                return this.instance;
            },
            enumerable: true,
            configurable: true
        });
        FrontController.prototype.renderView = function (viewName) {
            console.log("Rendering: " + viewName);
        };
        return FrontController;
    }());
    MyFramework.FrontController = FrontController;
})(MyFramework || (MyFramework = {}));
