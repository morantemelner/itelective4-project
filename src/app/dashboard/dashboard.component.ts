import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  modalActive: boolean = false;
  constructor() { 
  }

  ngOnInit(): void {
  }
  openPostModal(): void{
    this.modalActive = true;
  }
  closePostModal(): void{
    this.modalActive = false;
  }
}
