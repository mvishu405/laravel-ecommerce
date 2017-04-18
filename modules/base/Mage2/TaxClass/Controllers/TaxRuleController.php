<?php
namespace Mage2\TaxClass\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Mage2\Framework\System\Controllers\Controller;
use Mage2\TaxClass\Models\TaxRule;
use Illuminate\Support\Facades\Response;


class TaxRuleController extends Controller
{

    public function getTaxAmount(Request $request) {

        $taxRules = TaxRule::orderBy('priority','ASC')->get();
        $fieldName = $request->get('name');
        $fieldValue = $request->get('value');

        $findRule = null;
        foreach ($taxRules as  $taxRule) {
            if(strtolower($taxRule->$fieldName) == strtolower($fieldValue) ||
                    strtolower($taxRule->$fieldName) == "*") {
                $findRule = $taxRule;
                break;
            }
        }

        if(null === $findRule) {
            return Response::json(['success' => false]);
        }
        $cartItems = Session::get('cart');

        $taxAmount = 0;

        foreach($cartItems as $item) {

            $taxAmount += ($item['price'] * $taxRule->percentage ) / 100;
        }

        return Response::json(['success' => true, 'tax_amount' => "$" . number_format($taxAmount,2)]);
    }
}