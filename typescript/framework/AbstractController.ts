namespace MyFramework {
	export abstract class AbstractController {
		forwardToView(viewName : string): void {
			let fc = FrontController.getInstance();
			fc.renderView(viewName);
		}
	}
}