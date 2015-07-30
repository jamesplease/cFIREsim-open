<!DOCTYPE html>
<html lang="en" ng-app="cFIREsim">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <meta name="description" content="A Crowdsourced Financial Independence and Early Retirement Simulator and Calculator. Uses historic stock data to model your retirement and give you a success rate based on all of the possible periods of time in the stock market (good and bad)."><title>Crowdsourced Financial Independence and Early Retirement Simulator/Calculator</title>
     <style>
    .dygraph-axis-label-y { padding-right:10px; padding-left:10px;}
    #labelsdiv > span { display: none; }
    #labelsdiv > span.highlight { display: inline; }
    #labelsdiv2 > span { display: none; }
    #labelsdiv2 > span.highlight { display: inline; }
	.popup {
        display: none;
        position: absolute;
        top: 5%;
        left: 5%;
        bottom: 5%;
        right: 5%;
        width: 90%;
        height: 100%;
        padding: 16px;
        border: 4px solid black;
        background-color: white;
        z-index:1002;
        overflow: auto;
	}
    </style>
    <script src='http://code.jquery.com/jquery-1.10.2.min.js' language='Javascript' type='text/javascript'></script>
    <script type="text/javascript" src="http://dygraphs.com/dygraph-combined.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-select.min.js"></script> 
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.7/angular.min.js"></script>
	<?php
		echo '<script type="text/javascript" src="js/cFIREsimOpen.js"></script>';
		echo '<script type="text/javascript" src="js/formData-stub.js?v='.time().'"></script>';
		echo '<script type="text/javascript" src="js/marketData.js?v='.time().'"></script>';
		echo '<script type="text/javascript" src="js/spendingModule.js?v='.time().'"></script>';
		echo '<script type="text/javascript" src="js/statsModule.js?v='.time().'"></script>';
	?>





    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-select.min.css" rel="stylesheet">
  </head>
<body>
    <div class="page-header">
    	<h1 class="text-center">The Crowdsourced FIRE Simulator (cFIREsim) - Open Source</h1>
    </div>
    <div ng-controller="simulationInputController">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Basics  <button type="button" class="btn btn-success btn-sm" id="loadSimBtn">Load Saved Sim</button>
                    </div>
                    <div class="panel-body">
                        <label>Retirement Year:<input type="text" class="form-control" ng-model="data.retirementStartYear"></label>
                        <label>Retirement End Year:<input type="text" class="form-control" ng-model="data.retirementEndYear"></label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Data Options
                    </div>
                    <div class="panel-body">
                        <label>Data To Use:
                            <select class="form-control"
                                    ng-model="data.data.method"
                                    ng-change="refreshDataForm()"
                                    ng-options="dataOptions.value as dataOptions.text for dataOptions in dataOptionTypes">
                            </select>                                
                        </label>
                        <div id="historicalSpecificOptions" class="dataOptions" style="display:none">
                            <label>Starting Data Year:
                                <div class="input-group">
                                    <input type="text" class="form-control" ng-model="data.data.start">
                                </div>
                            </label>
                            <label>Ending Data Year:
                                <div class="input-group">
                                    <input type="text" class="form-control" ng-model="data.data.end">
                                </div>
                            </label>
                        </div>
                        <div id="constantGrowthOptions" class="dataOptions" style="display:none">
                            <label>Market Growth:
                                <div class="input-group">
                                    <input type="text" class="form-control" ng-model="data.data.growth">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Investigate:
                                    <select class="form-control"
                                            ng-model="data.investigate.type"
                                            ng-change="refreshInvestigateForm()"
                                            ng-options="investigateOptions.value as investigateOptions.text for investigateOptions in investigateOptionTypes">
                                    </select>                                
                                </label>
                                <div id="singleCycleOptions" class="dataOptions" style="display:none">
                                    <label>Starting Data Year:
                                        <div class="input-group">
                                            <input type="text" class="form-control" ng-model="data.investigate.single">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary" id="portfolioPanel">
                    <div class="panel-heading">
                        Portfolio
                    </div>
                    <div class="panel-body">
                        <label>Portfolio Value:<input type="text" class="form-control" ng-model="data.portfolio.initial"></label>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Initial Assets
                            </div>
                            <div class="panel-body">  
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Equities:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.percentEquities">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Bonds:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.percentBonds">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Gold:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.percentGold">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Cash:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.percentCash">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Fees:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.percentFees">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Growth of Cash:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.growthOfCash">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>  
                                    </div>
                                </div>               
                            </div>
                        </div>

                        <div class="row" style="display:none">
                            <div class="col-md-6">
                                <div class="form-group">       
                                    <label>Rebalance Annually:</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="rebalanceProtfolioRadio" value="true" ng-model="data.portfolio.rebalanceAnnually" ng-value="true" ng-change="enableRebalancing(true)">Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="rebalanceProtfolioRadio" value="false" ng-model="data.portfolio.rebalanceAnnually" ng-value="false" ng-change="enableRebalancing(false)">No
                                        </label>
                                    </div>  
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">       
                                    <label>Keep Allocation Constant:</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="constantAllocationRadio" value="true" ng-model="data.portfolio.constantAllocation" ng-value="true" ng-change="enableChangeAllocation(true)">Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="constantAllocationRadio" value="false" ng-model="data.portfolio.constantAllocation" ng-value="false" ng-change="enableChangeAllocation(false)">No
                                        </label>
                                    </div>  
                                </div>
                            </div>
                        </div>


                        <div class="panel panel-default" style="display:none">
                            <div class="panel-heading">
                                Target Assets
                            </div>
                            <div class="panel-body">  
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Start Year:
                                                <input type="text" class="form-control" ng-model="data.portfolio.changeAllocationStartYear">
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>End Year:
                                                <input type="text" class="form-control" ng-model="data.portfolio.changeAllocationEndYear">
                                        </label>
                                    </div>                                    
                                </div><div class="row">
                                    <div class="col-md-6">
                                        <label>Equities:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.targetPercentEquities">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Bonds:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.targetPercentBonds">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Gold:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.targetPercentEquities">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Cash:
                                            <div class="input-group">
                                                <input type="text" class="form-control" ng-model="data.portfolio.targetPercentBonds">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </label>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Spending Plan
                    </div>
                    <div class="panel-body">
                        <label>Spending Plan:
                            <select class="form-control"
                                    ng-model="data.spending.method"
                                    ng-change="refreshSpendingForm()"
                                    ng-options="spendingPlan.value as spendingPlan.text for spendingPlan in spendingPlanTypes">
                            </select>                                
                        </label>
                        <div id="yearlySpendingOptions" class="spendingOptions">
                            <label>Yearly Spending:
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" class="form-control" ng-model="data.spending.initial">
                                </div>
                            </label>
                        </div>
                        <div id="percentageOfPortfolioOptions" class="spendingOptions">
                            <label>Yearly Spending (% of portfolio):
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.percentageOfPortfolioPercentage">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                            <label>Percentage Type:
                                <div class="input-group">
                                    <select class="form-control"
                                            ng-model="data.spending.percentageOfPortfolioType"
                                            ng-change="clearFields('#percentageOfPortfolioLimits')"
                                            ng-options="percentageType.value as percentageType.text for percentageType in percentageOfPortfolioTypes">
                                    </select>  
                                </div>                              
                            </label>
                            <div id="percentageOfPortfolioLimits" ng-show="data.spending.percentageOfPortfolioType == 'withFloorAndCeiling'">
                                <label>Floor Spending:
                                    <div class="input-group">
                                        <select class="form-control"
                                                ng-model="data.spending.percentageOfPortfolioFloorType"
                                                ng-change="clearProperty(data.spending.percentageOfPortfolioFloorType == 'none', 'data.spending.percentageOfPortfolioFloorPercentage'); changeLabel()"
                                                ng-options="limitType.value as limitType.text for limitType in percentOfPortfolioFloorLimitTypes">
                                        </select>     
                                    </div>                           
                                </label>
                                <label>Never Less Than:
                                    <div class="input-group">
                                        <input type="text"
                                                class="form-control"
                                                ng-model="data.spending.percentageOfPortfolioFloorPercentage"
                                                ng-disabled="data.spending.percentageOfPortfolioFloorType == 'none'">
                                        <span class="input-group-addon spending-floor-span">%</span>
                                    </div>
                                </label>
                                <br>                              
                                <label>Ceiling Spending:
                                    <div class="input-group">
                                        <select class="form-control"
                                                ng-model="data.spending.percentageOfPortfolioCeilingType"
                                                ng-change="clearProperty(data.spending.percentageOfPortfolioCeilingType == 'none', 'data.spending.percentageOfPortfolioCeilingPercentage')"
                                                ng-options="limitType.value as limitType.text for limitType in percentOfPortfolioCeilingLimitTypes">
                                        </select>   
                                    </div>                             
                                </label>
                                <label>Never More Than:
                                    <div class="input-group">
                                        <input  type="text"
                                                class="form-control"
                                                ng-model="data.spending.percentageOfPortfolioCeilingPercentage"
                                                ng-disabled="data.spending.percentageOfPortfolioCeilingType == 'none'">
                                        <span class="input-group-addon spending-ceiling-span">%</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div id="hebelerAutopilotOptions" class="spendingOptions">
                            <label>Age of Retirement:
                                    <input type="text" class="form-control" ng-model="data.spending.hebelerAgeOfRetirement">
                            </label>
                            <label>Weighted RMD:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.hebelerWeightedRMD">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                            <label>Weighted CPI:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.hebelerWeightedCPI">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>

                        </div>
                        <div id="variableSpendingOptions" class="spendingOptions">
                            <label>Z Value (0.000 - 1.000):
                                    <input type="text" class="form-control" ng-model="data.spending.variableSpendingZValue">
                            </label>
                        </div>
                        <div id="guytonKlingerOptions" class="spendingOptions">                      
                            <label>Exceeds:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.guytonKlingerExceeds">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                            <label>Cut:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.guytonKlingerCut">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                            <br>
                            <label>Fall:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.guytonKlingerFall">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                            <label>Raise:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.guytonKlingerRaise">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                        </div>
                        <div id="retireAgainAndAgainOptions" class="spendingOptions">
                            <label>RAA Target Portfolio Amount:
                                <div class="input-group">
                                    <select class="form-control"
                                            ng-model="data.spending.retireAgainAmountType"
                                            ng-options="retireAgainAmountType.value as retireAgainAmountType.text for retireAgainAmountType in retireAgainAmountTypes">
                                    </select>        
                                </div>                        
                            </label>                                    
                            <label>RAA Custom Portfolio Target:
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.retireAgainCustomTarget">
                                </div>
                            </label>
                            <label>Threshold for Spending Increase:
                                <div class="input-group">
                                    <input  type="text"
                                            class="form-control"
                                            ng-model="data.spending.retireAgainThreshold">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </label>
                        </div>
                        <div id="customVPWOptions" class="spendingOptions">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                   <p>The PMT function used in VPW is calculated based on the "Years to model" and your actual retirement year. The Initial Withdrawal Rate is based on the portfolio during the first year of retirement, and your "Yearly Spending" listed in the Spending Plan section.</p>
                                </div>
                            </div>
                        </div>
                        <div id="spendingLimitOptions" class="spendingOptions">
                            <label>Spending Floor (Inflation Adjusted):          
                                <select class="form-control"
                                        ng-model="data.spending.floor"
                                        ng-options="floor.value as floor.text for floor in spendingFloorTypes">
                                </select>    
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input  type="text"
                                        class="form-control"
                                        ng-model="data.spending.floorValue"
                                        ng-disabled="data.spending.floor != 'definedValue'">
                            </div>
                            <label>Spending Ceiling (Inflation Adjusted):     
                                <select class="form-control"
                                        ng-model="data.spending.ceiling"
                                        ng-options="ceiling.value as ceiling.text for ceiling in spendingCeilingTypes">
                                </select>    
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input  type="text"
                                        class="form-control"
                                        ng-model="data.spending.ceilingValue"
                                        ng-disabled="data.spending.ceiling != 'definedValue'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Extra Income/Savings
                    </div>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Social Security
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Annual:
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" class="form-control" ng-model="data.extraIncome.socialSecurity.val">
                                            </div>
                                        </label>
                                    </div> 
                                    <div class="col-md-4">
                                        <label>Start Year:<input type="text" class="form-control" ng-model="data.extraIncome.socialSecurity.startYear"></label>       
                                    </div>                            
                                    <div class="col-md-4">
                                        <label>End Year:<input type="text" class="form-control" ng-model="data.extraIncome.socialSecurity.endYear"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Annual Spousal:
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" class="form-control" ng-model="data.extraIncome.socialSecuritySpouse.val">
                                            </div>
                                        </label>
                                    </div> 
                                    <div class="col-md-4">
                                        <label>Start Year:<input type="text" class="form-control" ng-model="data.extraIncome.socialSecuritySpouse.startYear"></label>       
                                    </div>                            
                                    <div class="col-md-4">
                                        <label>End Year:<input type="text" class="form-control" ng-model="data.extraIncome.socialSecuritySpouse.endYear"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Pensions
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                Label
                                            </th>
                                            <th>
                                                Amount ($)
                                            </th>
                                            <th>
                                                Start Year
                                            </th>
                                            <th>
                                                Inflation Adjusted
                                            </th>
                                            <th>
                                                Inflation Type
                                            </th>
                                            <th>
                                                Inflation %
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="pension in data.extraIncome.pensions">
                                            <td>
                                                <input type="text" class="form-control" ng-model="pension.label">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="pension.val">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="pension.startYear">
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="pension.inflationAdjusted"
                                                        ng-options="bool for bool in boolOptions"
                                                        ng-change="changeInflationAdjusted($index, data.extraIncome.pensions)">
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="pension.inflationType"
                                                        ng-options="option.value as option.text for option in inflationTypes"
                                                        ng-change="changeInflationType($index, data.extraIncome.pensions)"
                                                        ng-disabled="!pension.inflationAdjusted">
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                        class="form-control"
                                                        ng-model="pension.inflationRate"
                                                        ng-disabled="pension.inflationType != 'constant'">
                                            </td>
                                            <td>
                                                <input type="button" ng-click="removeObject($index, data.extraIncome.pensions)" value="Delete"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="button" ng-click="addObject(data.extraIncome.pensions)" value="Add Pension"/>                                
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Other Income
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                Label
                                            </th>
                                            <th>
                                                Amount ($)
                                            </th>
                                            <th>
                                                Recurring
                                            </th>
                                            <th>
                                                Start year
                                            </th>
                                            <th>
                                                End Year
                                            </th>
                                            <th>
                                                Inflation Adjusted
                                            </th>
                                            <th>
                                                Inflation Type
                                            </th>
                                            <th>
                                                Inflation %
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="extraSaving in data.extraIncome.extraSavings">
                                            <td>
                                                <input type="text" class="form-control" ng-model="extraSaving.label">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="extraSaving.val">
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="extraSaving.recurring"
                                                        ng-options="bool for bool in boolOptions"
                                                        ng-change="clearEndYear($index, data.extraIncome.extraSavings)">
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="extraSaving.startYear">
                                            </td>
                                            <td>
                                                <input  type="text"
                                                        class="form-control"
                                                        ng-model="extraSaving.endYear"
                                                        ng-disabled="!extraSaving.recurring">
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="extraSaving.inflationAdjusted"
                                                        ng-options="bool for bool in boolOptions"
                                                        ng-change="changeInflationAdjusted($index, data.extraIncome.extraSavings)">
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="extraSaving.inflationType"
                                                        ng-options="option.value as option.text for option in inflationTypes"
                                                        ng-change="changeInflationType($index, data.extraIncome.extraSavings)"
                                                        ng-disabled="!extraSaving.inflationAdjusted">
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                        class="form-control"
                                                        ng-model="extraSaving.inflationRate"
                                                        ng-disabled="extraSaving.inflationType != 'constant'">
                                            </td>
                                            <td>
                                                <input type="button" ng-click="removeObject($index, data.extraIncome.extraSavings)" value="Delete"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="button" ng-click="addObject(data.extraIncome.extraSavings)" value="Add Savings"/>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Extra Spending
                    </div>
                    <div class="panel-body">
                    <div class="panel panel-default">
                            <div class="panel-heading">
                                Other Spending
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                Label
                                            </th>
                                            <th>
                                                Amount ($)
                                            </th>
                                            <th>
                                                Recurring
                                            </th>
                                            <th>
                                                Start year
                                            </th>
                                            <th>
                                                End Year
                                            </th>
                                            <th>
                                                Inflation Adjusted
                                            </th>
                                            <th>
                                                Inflation Type
                                            </th>
                                            <th>
                                                Inflation %
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="extraSpending in data.extraSpending">
                                            <td>
                                                <input type="text" class="form-control" ng-model="extraSpending.label">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="extraSpending.val">
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="extraSpending.recurring"
                                                        ng-options="bool for bool in boolOptions"
                                                        ng-change="clearEndYear($index, data.extraSpending)">
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="extraSpending.startYear">
                                            </td>
                                            <td>
                                                <input  type="text"
                                                        class="form-control"
                                                        ng-model="extraSpending.endYear"
                                                        ng-disabled="!extraSpending.recurring">
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="extraSpending.inflationAdjusted"
                                                        ng-options="bool for bool in boolOptions"
                                                        ng-change="changeInflationAdjusted($index, data.extraSpending)">
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        ng-model="extraSpending.inflationType"
                                                        ng-options="option.value as option.text for option in inflationTypes"
                                                        ng-change="changeInflationType($index, data.extraSpending)"
                                                        ng-disabled="!extraSpending.inflationAdjusted">
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                        class="form-control"
                                                        ng-model="extraSpending.inflationRate"
                                                        ng-disabled="extraSpending.inflationType != 'constant'">
                                            </td>
                                            <td>
                                                <input type="button" ng-click="removeObject($index, data.extraSpending)" value="Delete"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="button" ng-click="addObject(data.extraSpending)" value="Add Spending"/>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="button" value="Run Simulation" ng-click="runSimulation()">
        <!-- Modal -->
		<div class="modal fade" id="outputModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog modal-lg" role="document">
		    <div class="modal-content">
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      
		    </div>
		  </div>
		</div>
    </div>
    <div id="outputPopup" style="display:none" class="popup">
    	<div id='graphdiv' style='width:1100px; height:550px;background:white'></div>
    	<div id="labelsdiv" style="background:white;width:1100px;height:20px;"></div>
    </div>
    <script type="text/javascript" src="js/marketData.js"></script>
    <script type="text/javascript" src="js/cFIREsimOpen.js"></script>
</body>
</html>
<script type="text/javascript">
    angular.module('cFIREsim', [])
        .controller('simulationInputController', ['$scope', function($scope) {
            $scope.data = {
                retirementStartYear: 2015,
                retirementEndYear: 2044,
                data: {
                    method: "historicalAll",
                    start: 1900,
                    end: 1970,
                    growth: 8
                },
                investigate: {
                    type: "none",
                    single: 1966
                },
                portfolio: {
                    initial: 1000000,
                    percentEquities: 75,
                    percentBonds: 25,
                    percentGold: 0,
                    percentCash: 0,
                    percentFees: 0.18,
                    growthOfCash: 0.25,
                    rebalanceAnnually: true,
                    constantAllocation: true
                },
                spending: {
                    initial: 40000,
                    method: 'inflationAdjusted',
                    floor: 'pensions',
                    ceiling: 'none',
                    percentageOfPortfolioType: 'constant',
                    percentageOfPortfolioFloorType: 'percentageOfPortfolio',
                    percentageOfPortfolioCeilingType: 'percentageOfPreviousYear',
                    percentageOfPortfolioFloorPercentage: 7,
                    percentageOfPortfolioPercentage: 4,
                    retireAgainAmountType: 'valueAtRetirement',
                    hebelerAgeOfRetirement: 60, 
                    hebelerWeightedCPI: 50,
                    hebelerWeightedRMD: 50,
                    variableSpendingZValue: 0.5,
                    guytonKlingerExceeds: 20,
                    guytonKlingerFall: 20,
                    guytonKlingerRaise: 10,
                    guytonKlingerCut: 10
                },
                extraIncome: {
                    socialSecurity: {
                        val: 0,
                        startYear: 2032,
                        endYear: 2100
                    },
                    socialSecuritySpouse: {
                        val: 0,
                        startYear: 2032,
                        endYear: 2100
                    },
                    pensions: [{
                        label: 'One',
                        val: 0,
                        startYear: 2030,
                        endYear: null,
                        recurring: true,
                        inflationAdjusted: true,
                        inflationType: 'CPI',
                        inflationRate: ''
                    },{
                        label: 'Two',
                        val: 0,
                        startYear: 2030,
                        endYear: null,
                        recurring: true,
                        inflationAdjusted: true,
                        inflationType: 'CPI',
                        inflationRate: ''
                    }],
                    extraSavings: [{
                        label: 'One',
                        val: 0,
                        startYear: 2030,
                        endYear: 2035,
                        recurring: true,
                        inflationAdjusted: true,
                        inflationType: 'CPI',
                        inflationRate: ''
                    },{
                        label: 'Two',
                        val: 0,
                        startYear: 2031,
                        endYear: 2041,
                        recurring: true,
                        inflationAdjusted: true,
                        inflationType: 'CPI',
                        inflationRate: ''
                    }]
                },
                extraSpending: [{
                    label: 'One',
                    val: 0,
                    startYear: 2030,
                    endYear: 2040,
                    recurring: true,
                    inflationAdjusted: true,
                    inflationType: 'CPI',
                    inflationRate: ''
                },{
                    label: 'Two',
                    val: 0,
                    startYear: 2030,
                    endYear: 2041,
                    recurring: true,
                    inflationAdjusted: true,
                    inflationType: 'CPI',
                    inflationRate: ''
                }]
            }

            $scope.boolOptions = [
                true,
                false
            ]

            $scope.inflationTypes = [            
                {
                    text: 'CPI',
                    value: 'CPI'
                },
                {
                    text: 'Constant %',
                    value: 'constant'
                }
            ]

            $scope.dataOptionTypes = [
                {
                    text: 'Historical Data - All',
                    value: 'historicalAll'
                },
                {
                    text: 'Historical Data - Specific Years',
                    value: 'historicalSpecific',
                    formInputs: [
                        'historicalSpecificOptions'
                    ]
                },
                {
                    text: 'Constant Market Growth',
                    value: 'constant',
                    formInputs: [
                        'constantGrowthOptions'
                    ]
                }
            ]

            $scope.investigateOptionTypes = [
                {
                    text: 'None ',
                    value: 'none',
                },
                {
                    text: 'Single Simulation Cycle ',
                    value: 'single',
                    formInputs: [
                        'singleCycleOptions'
                    ]
                }
            ]

            $scope.spendingPlanTypes = [
                {
                    text: 'Inflation Adjusted',
                    value: 'inflationAdjusted',
                    formInputs: [
                        'yearlySpendingOptions'
                    ]
                },{
                    text: 'Not Inflation Adjusted',
                    value: 'notInflationAdjusted',
                    formInputs: [
                        'yearlySpendingOptions',
                        'spendingLimitOptions'
                    ]
                },{
                    text: '% of Portfolio',
                    value: 'percentOfPortfolio',
                    formInputs: [
                        'percentageOfPortfolioOptions'
                    ]
                },{
                    text: 'Hebeler Autopilot',
                    value: 'hebelerAutopilot',
                    formInputs: [
                        'yearlySpendingOptions',
                        'hebelerAutopilotOptions',
                        'spendingLimitOptions'
                    ]
                },{
                    text: 'Variable Spending',
                    value: 'variableSpending',
                    formInputs: [
                        'yearlySpendingOptions',
                        'variableSpendingOptions',
                        'spendingLimitOptions'
                    ]
                },{
                    text: 'Guyton-Klinger',
                    value: 'guytonKlinger',
                    formInputs: [
                        'yearlySpendingOptions',
                        'guytonKlingerOptions',
                        'spendingLimitOptions'
                    ]
                }/*,{
                    text: 'Retire Again and Again',
                    value: 'retireAgainAndAgain',
                    formInputs: [
                        'yearlySpendingOptions',
                        'retireAgainAndAgainOptions',
                        'spendingLimitOptions'
                    ]
                }
                ,{
                    text: 'Original VPW',
                    value: 'originalVPW',
                    formInputs: [
                        'spendingLimitOptions'
                    ]
                },{
                    text: 'Custom VPW',
                    value: 'customVPW',
                    formInputs: [
                        'yearlySpendingOptions',
                        'customVPWOptions',
                        'spendingLimitOptions'
                    ]
                }*/
            ]

            $scope.spendingFloorTypes = [
                {
                    text: 'Pensions/SS',
                    value: 'pensions'
                },
                {
                    text: 'Defined Value',
                    value: 'definedValue'
                },
                {
                    text: 'No Floor',
                    value: 'none'
                }
            ]

            $scope.spendingCeilingTypes = [
                {
                    text: 'No Ceiling',
                    value: 'none'
                },
                {
                    text: 'Defined Value',
                    value: 'definedValue'
                }
            ]

            $scope.percentageOfPortfolioTypes = [
                {
                    text: 'Constant %',
                    value: 'constant'
                },
                {
                    text: 'With Floor and Ceiling Values',
                    value: 'withFloorAndCeiling'
                }
            ]

            $scope.percentOfPortfolioFloorLimitTypes = [
                {
                    text: 'As a % of Starting Portfolio',
                    value: 'percentageOfPortfolio'
                },
                {
                    text: '% of Previous Year',
                    value: 'percentageOfPreviousYear'
                },
                {
                    text: 'Defined $ value',
                    value: 'definedValue'
                },
                {
                    text: 'No Limit',
                    value: 'none'
                }
            ]

            $scope.percentOfPortfolioCeilingLimitTypes = [
                {
                    text: 'As a % of Starting Portfolio',
                    value: 'percentageOfPortfolio'
                },
                {
                    text: 'No Limit',
                    value: 'none'
                }
            ]

            $scope.retireAgainAmountTypes = [
                {
                    text: 'Portfolio Value at Retirement',
                    value: 'valueAtRetirement'
                },
                {
                    text: 'Custom Portfolio Value',
                    value: 'customValue'
                }
            ]

            // Refreshes the spending form by hiding all the sections, showing the correct ones, then wiping the data in the still hidden ones.
            $scope.refreshSpendingForm = function () {
                var spendingPlan = $.grep($scope.spendingPlanTypes, function (spendingPlanType) {
                    return spendingPlanType.value == $scope.data.spending.method
                });

                $('.spendingOptions').hide();

                if (spendingPlan.length == 1) {
                    $.each(spendingPlan[0].formInputs, function (index, formInput) {
                        $('#' + formInput).show();
                    });
                }

                //Removed clearing of fields, so that initial spending value wouldn't disappear when percentOfPortfolio was selected. I don't think this adversely affects the code, but will leave this for posterity. 
                //$scope.clearFields('.spendingOptions:hidden');
            }

            $scope.refreshDataForm = function () {
                if($scope.data.data.method == "historicalSpecific"){
                   $('#historicalSpecificOptions').show(); 
                   $('#constantGrowthOptions').hide(); 
                }else if($scope.data.data.method == "constant"){
                    $('#constantGrowthOptions').show();
                    $('#historicalSpecificOptions').hide();
                }else if($scope.data.data.method == "historicalAll"){
                    $('#constantGrowthOptions').hide();
                    $('#historicalSpecificOptions').hide();
                }
            }
            $scope.refreshInvestigateForm = function () {
                if($scope.data.investigate.type == "single"){
                    $('#singleCycleOptions').show(); 
                }else{
                    $('#singleCycleOptions').hide();
                }
                
            } 

            // Clears all the inputs and selects within the items returned by the selector.
            $scope.clearFields = function (selector) {
                // For the spending option sections which are still hidden, clear their values.
                $.each($(selector), function (index, option) {
                    $('input', option).val('');
                    $('select', option).prop('selectedIndex', 0);

                    // If called without the timeout Angular with throw and error the first time this function is called.
                    setTimeout(
                        function () {
                            $('select', option).trigger('change')
                        }, 0, option);
                });
            }

            $scope.runSimulation = function () {
                Simulation.runSimulation($scope.data);
            }

            $scope.saveSimulation = function () {
                console.log($scope.data);

                var uri = 'data:text/csv;charset=utf-8,' + JSON.stringify($scope.data);
                var link = document.createElement("a");
                link.href = uri;

                //set the visibility hidden so it will not effect on your web-layout
                link.style = "visibility:hidden";
                link.download = "simulation.json";

                //this part will append the anchor tag and remove it after automatic click
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            $scope.loadSimulation = function () {
               // $scope.data =   
               //Load from JSON
            }

            // TODO: Could these 2 be turned into declarative Angular statements?
            $scope.enableRebalancing = function (enable) {
                $('#portfolioPanel [name=constantAllocationRadio]').attr('disabled', !enable);
                $scope.enableChangeAllocation(enable);
            }

            $scope.enableChangeAllocation = function (enable) {
                var inputs = $('#targetAssetsPanel input');

                if (!enable) {
                    inputs.val('');
                }

                inputs.attr('disabled', !enable);
            }

            // Adds a saving or pension object.
            $scope.addObject = function (array) {
                array.push({
                    label: '',
                    val: 0,
                    startYear: 2030,
                    endYear: 2035,
                    recurring: true,
                    inflationAdjusted: true,
                    inflationType: 'CPI',
                    inflationRate: ''
                });
            }

            $scope.removeObject = function (index, array) {
                array.splice(index, 1);
            }

            // TODO: These there methods which clear a property of an object in an array could probably be generalized.
            $scope.changeInflationAdjusted = function (index, array) {
                var object = array[index];

                if (!object.inflationAdjusted) {
                    object.inflationType = '';
                }
                $scope.changeInflationType(index, array);
            }

            $scope.changeInflationType = function (index, array) {
                var object = array[index];

                if (object.inflationType != 'constant') {
                    object.inflationRate = '';
                }
            }

            $scope.clearEndYear = function (index, array) {
                var object = array[index];

                if (!object.recurring) {
                    object.endYear = '';
                }
            }

            $scope.clearProperty = function (clear, path) {
                if (clear) {
                    var pathArray = path.split('.');
                    var property = $scope;

                    for (var i = 0; i < pathArray.length; i++) {
                        if (i == (pathArray.length - 1)) {
                            property[pathArray[i]] = '';
                        }
                        else {
                            property = property[pathArray[i]];                            
                        }
                    }
                }
            }
            $scope.changeLabel = function (label){
            	if($scope.data.spending.percentageOfPortfolioFloorType == "definedValue"){
            		$(".spending-floor-span").html("$");
            	}else{
            		$(".spending-floor-span").html("%");
            	}
            	if($scope.data.spending.percentageOfPortfolioCeilingType == "definedValue"){
            		$(".spending-ceiling-span").html("$");
            	}else{
            		$(".spending-ceiling-span").html("%");
            	}
            }

            // Setup the spending form when the controller loads.
            $scope.refreshSpendingForm()
        }]);

</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42984907-1', 'auto');
  ga('send', 'pageview');

</script>