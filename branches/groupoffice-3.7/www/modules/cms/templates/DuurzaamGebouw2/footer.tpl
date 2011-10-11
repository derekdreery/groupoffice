	</div>
	<div style="clear:both;"></div>		
	<div id="footer">
		<div id="footer-panel1" class="footer-panel">
			{items root_path="Footer" wrap_div="false" item_template="parts/footer_contact.tpl"}
		</div>
		<div id="footer-panel2" class="footer-panel">
			<div class="footer-title">Diensten</div>
			<div class="footer-items">
				{items wrap_div="false" root_path="MENU1/Diensten" max_items="3" item_template="parts/item_name.tpl"}
			</div>
		</div>
		<div id="footer-panel3" class="footer-panel">
			<div class="footer-title">Projecten</div>
			<div class="footer-items">
				{items wrap_div="false" root_path="Projecten" max_items="3" item_template="parts/item_name.tpl"}
			</div>
		</div>
		<div id="footer-panel4" class="footer-panel">
			<div class="footer-title">In de pers</div>
			<div class="footer-items">
				{items wrap_div="false" root_path="Footer/In de pers" max_items="3" item_template="parts/item_name.tpl"}
			</div>
		</div>
		<div id="footer-panel5" class="footer-panel">
			<div class="footer-title">Volg ons</div>
			<div class="footer-items">
				<a class="footer-icon" target="_blank" href="http://twitter.com/#!/Duurzaamgebouw"><img src="{$template_url}images/twitter_icon.jpg" /></a>
				<a class="footer-icon" target="_blank" href="http://www.linkedin.com/groups/DuurzaamGebouw-3750417"><img src="{$template_url}images/linkedin_icon.jpg" /></a>
			</div>
		</div>
	</div>
</div><!-- main-container -->
</body>
</html>
