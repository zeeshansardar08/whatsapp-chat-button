( function () {
	'use strict';

	var updateRowTargetState = function ( row ) {
		var ruleTypeSelect = row.querySelector( '[data-wacb-rule-type]' );
		var activeRuleType = ruleTypeSelect ? ruleTypeSelect.value : 'page';
		var targetWraps = row.querySelectorAll( '[data-wacb-target-wrap]' );

		targetWraps.forEach( function ( wrap ) {
			if ( wrap.getAttribute( 'data-wacb-target-wrap' ) === activeRuleType ) {
				wrap.classList.add( 'is-active' );
				return;
			}

			wrap.classList.remove( 'is-active' );
		} );
	};

	document.addEventListener( 'DOMContentLoaded', function () {
		var routingRoot = document.querySelector( '[data-wacb-routing-rules]' );

		if ( ! routingRoot ) {
			return;
		}

		var rulesBody = routingRoot.querySelector( '[data-wacb-rules-body]' );
		var addButton = routingRoot.querySelector( '[data-wacb-add-rule]' );
		var templateNode = routingRoot.querySelector( '[data-wacb-rule-template]' );
		var nextIndex = parseInt( routingRoot.getAttribute( 'data-wacb-next-index' ) || '0', 10 );

		if ( ! rulesBody || ! addButton || ! templateNode ) {
			return;
		}

		rulesBody.querySelectorAll( '[data-wacb-rule-row]' ).forEach( updateRowTargetState );

		routingRoot.addEventListener( 'change', function ( event ) {
			var target = event.target.closest( '[data-wacb-rule-type]' );

			if ( ! target ) {
				return;
			}

			updateRowTargetState( target.closest( '[data-wacb-rule-row]' ) );
		} );

		routingRoot.addEventListener( 'click', function ( event ) {
			var removeButton = event.target.closest( '[data-wacb-remove-rule]' );

			if ( removeButton ) {
				var row = removeButton.closest( '[data-wacb-rule-row]' );

				if ( row ) {
					row.remove();
				}

				return;
			}

			if ( ! event.target.closest( '[data-wacb-add-rule]' ) ) {
				return;
			}

			var templateHtml = templateNode.textContent.split( '__index__' ).join( String( nextIndex ) );
			var template = document.createElement( 'template' );

			template.innerHTML = templateHtml.trim();

			if ( template.content.firstElementChild ) {
				rulesBody.appendChild( template.content.firstElementChild );
				updateRowTargetState( rulesBody.lastElementChild );
				nextIndex += 1;
				routingRoot.setAttribute( 'data-wacb-next-index', String( nextIndex ) );
			}
		} );
	} );
}() );
