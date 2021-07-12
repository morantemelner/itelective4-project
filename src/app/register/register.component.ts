import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../service/data.service';


@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {
  
  fname: any;
  mname: any;
  lname: any;
  role: any;
  email: any;
  password: any;
  confirm_password: any;
  
  constructor(private dataService: DataService, private router: Router) { }

  ngOnInit(): void {
  }

  accountInformation: any = {};

  registerAccount(){
    this.accountInformation.acc_email = this.email;
    this.accountInformation.acc_password = this.password;
    this.accountInformation.acc_fname = this.fname;
    this.accountInformation.acc_mname = this.mname;
    this.accountInformation.acc_lname = this.lname;
    this.accountInformation.acc_role = this.role;

    if(this.accountInformation.acc_password == this.confirm_password){
      this.dataService.sendApiRequest('accountRegister', this.accountInformation).subscribe((data: { payload: any; }) => {
        if(data.payload == "exist!"){
          window.alert("Email Does Exist!");
        }else if (data.payload == this.email){
          window.alert("Success Register");
          this.router.navigate(['/login']);
        }
      });
    }else{
      console.log("Password did not match!");
    }
  }
  
}
