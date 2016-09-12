namespace MyFramework {
	export class FrontController {
		private static instance : FrontController;
		
		constructor(){}
		
		public get Instance() {
			if(this.instance == null || this.instance == undefined) {
				this.instance = new FrontContoller();
			}
			return this.instance;
		}
		
		public renderView(viewName : string) : void {
			console.log("Rendering: " + viewName);
		}
	}
}