import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../service/data.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  
  email: any;
  password: any;
  
  constructor(private dataService: DataService, private router: Router) { }

  ngOnInit(): void {
  }

  accountCredentials: any = {};

  loginAccount(){
    this.accountCredentials.acc_email = this.email;
    this.accountCredentials.acc_password = this.password;
    

    this.dataService.sendApiRequest('accountLogin', this.accountCredentials).subscribe((data: { payload: any; status: any; }) => {

      if(data.status['remarks'] == "success"){
          console.log(data.payload);
          window.alert("SUCESS!!!!!");
          this.router.navigate(['/dashboard'])
      }
      else if(data.status['remarks'] == "notExist"){
        window.alert("Not existing!");
      }else{
        window.alert("Wrong password!");
      }
    });
  }
}
