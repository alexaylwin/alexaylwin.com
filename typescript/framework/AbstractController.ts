namespace Framework {
	export abstract class AbstractController {
		abstract forwardToView(viewName : string): void;
	}
}