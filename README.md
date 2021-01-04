# optimizer
Calculates the distribution of goods on different vehicles based on use values using linear optimization. The constraint is the maximum payload of the vehicles.

## Lizence
This work is provided unter the terms of the MIT licence. Please take a look at the LICENSE file for the full text.

## Features
* Multiple vehicles for transportation can be defined.
* In addition to the maximum payload the weight of the drivers can be defined.
* Provide data using a csv file.
### Constraints of the algorithm
 * Payload of vehicles.
 * Demand of the goods.
### Chosen algorithm
Due to the linear optimization problem, George Dantzig's simplex algorithm is chosen including a round down at the end of the process. This produces values which are found to be optimal.

## Technology
These software versions are known to work:
* PHP 7.4
* Apache 2.4

## Installation
* Install and configure PHP and the Webserver.
* Download the project folder by cloning it from GitHub.
* Make the project folder accessible for the web server.

## Using 
This section describes, how to use the software.

First of all, please make sure that JavaScript is activated in your web browser.
### Provide information

The data is transferred to the software via two files. Information about the goods including weight, use value and demands has to be written in the `data_hardware.csv` file. Ones about the maximum payload and the weight of the drivers are stored in the `data_van.php` file. Every non-integer must be written with a point as a decimal separator.

The `data_hardware.csv` file has the following structure with one hardware name in each line:
```
<name>;<demand>;<weight of one unit in kg>;<use value>
[…]
```
The `data_van.php` file is an ordinary PHP file. While the maximum capacity is stored in an integer variable, the weighing values of the drivers are written to an array. The number of array elements also defines the number of vehicles used.

```
<?php
$capacity_max = <maximum payload in kg>;
$capacity_driver = array(<weight driver 1 in kg>[, <weight driver 2 in kg>[, …]]);
```
Examples of both files are stored in the application folder.

To check the provided information after changes were made you can press the *`Reload data`* button.

### Calculate optimal loading
To calculate the result just press the *`Process data`* button.
### Sample output
![Sample output of the application](sample_output.png)

## Contact
If you have any questions, just drop a message at thorres [at] brothersofgrey [dot] net.
